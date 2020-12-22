<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\Names;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\Artifact;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoPropertyDto;

/**
 * @description I hate this class, but I don't have time to make it easier
 * @todo readOnly/writeOnly, anyOf
 */
abstract class Base implements PropertyBuilderInterface
{
    protected function createClass(array $schema, BuildContext $context): ProtoClassDto
    {
        $name = $context->getNames()->joinNames($context->getJoinStrategy());

        $proto = new ProtoClassDto();

        if (!$context->getKnownObjects()->containsKey($name)) {
            $context->getKnownObjects()->set($name, $proto);
        }

        $properties = [];

        foreach ($schema['properties'] as $propertyName => $property) {
            $context->getNames()->pushName($propertyName);

            $properties[] =
                $this->createProperty(
                    $property,
                    $context,
                    !in_array($propertyName, $schema['required'] ?? [], true)
                );

            $context->getNames()->popName();
        }

        return
            $proto
                ->setName($name)
                ->setProperties($properties)
            ;
    }

    protected function createProperty(array $property, BuildContext $context, bool $nullable): ProtoPropertyDto
    {
        $protoProp = new ProtoPropertyDto();
        $protoProp
            ->setArtifact(new Artifact($property, $context->getSerializationGroups()))
            ->setName($context->getNames()->getName())
            ->setCollection(false)
            ->setScalar(true)
            ->setObjectType(null)
            ->setNullable($nullable)
        ;

        if (isset($property['type'])) {
            switch ($property['type']) {
                case 'string':
                    if (isset($property['format'])) {
                        switch ($property['format']) {
                            case 'date-time':
                                $protoProp
                                    ->setScalar(false)
                                    ->setScalarType('object')
                                    ->setObjectType(
                                        (new ProtoClassDto())
                                            ->setName('\DateTime')
                                    );

                                break;
                            default:
                                $protoProp->setScalarType('string');

                                break;
                        }
                    } else {
                        $protoProp->setScalarType('string');
                    }

                    break;
                case 'number':
                    $protoProp->setScalarType('float');
                    break;
                case 'int':
                case 'integer':
                    $protoProp->setScalarType('int');
                    break;
                case 'boolean':
                    $protoProp->setScalarType('bool');
                    break;
                case 'array':
                    if (isset($property['items'])) {
                        $context->getNames()->pushName('item');

                        $itemProp = $this->createProperty($property['items'], $context, $protoProp->isNullable());

                        $context->getNames()->popName();

                        $protoProp
                            ->setCollection(true)
                            ->setScalar($itemProp->isScalar())
                            ->setObjectType($itemProp->getObjectType())
                            ->setScalarType($itemProp->getScalarType())
                            ->setOthers($itemProp->getOthers())
                            ->setMultiple($itemProp->isMultiple())
                        ;
                    }
                    break;
                case 'object':
                    $protoProp
                        ->setScalar(false)
                        ->setObjectType($this->createClass($property, $context))
                        ->setScalarType('object')
                    ;
                    break;
            }
        } else {
            $object = $this->createObjectProperty($property, $protoProp, $context);
            $protoProp
                ->setScalar(false)
                ->setObjectType($object)
                ->setScalarType('object')
            ;
        }

        return $protoProp;
    }

    private function createObjectProperty(
        array $property,
        ProtoPropertyDto $protoProp,
        BuildContext $context
    ): ProtoClassDto {
        if (isset($property['type'])) {
            // @todo
            if ($property['type'] !== 'object') {
                throw new \RuntimeException("Non objects not allowed");
            }

            return $this->createClass($property, $context);
        }

        if (isset($property['$ref'])) {
            $ref = $context->getSchema()->getRef($property['$ref']);

            if ($ref->getName() !== $property['$ref']) { // schema/components object
                return
                    $this->createObjectProperty(
                        $ref->getObject(),
                        $protoProp,
                        $context->withNames(new Names([$ref->getName()]))
                    );
            }
        }

        if (isset($property['allOf'])) {
            $allProperties = [];

            foreach ($property['allOf'] as $type => $data) {
                $object = $this->createObjectProperty($data, $protoProp, $context);
                $allProperties = array_merge($allProperties, $object->getProperties());
            }

            $class =
                $this->createClass(['properties' => []], $context)
                    ->setProperties($allProperties);
            $context->getKnownObjects()->set($context->getNames()->joinNames($context->getJoinStrategy()), $class);
            return $class;
        } elseif (isset($property['oneOf'])) {
            $protoProp->setMultiple(true);
            $init = null;

            foreach ($property['oneOf'] as $type => $data) {
                $object = $this->createObjectProperty($data, $protoProp, $context);

                if ($init === null) {
                    $init = $object;
                } else {
                    $protoProp->addOther($object);
                }
            }

            return $init;
        } elseif (isset($property['anyOf'])) {
            $protoProp->setMultiple(true);
            $init = null;

            foreach ($property['anyOf'] as $type => $data) {
                $object = $this->createObjectProperty($data, $protoProp, $context);

                if ($init === null) {
                    $init = $object;
                } else {
                    $protoProp->addOther($object);
                }
            }

            return $init;
        }

        throw new \RuntimeException('Unknown data: '.var_export($property, true));
    }
}