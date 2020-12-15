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

        if ($context->getKnownObjects()->containsKey($name)) {
            return $context->getKnownObjects()->get($name);
        }

        $context->getKnownObjects()->set($name, $proto = new ProtoClassDto());

        $properties = [];

        foreach ($schema['properties'] as $propertyName => $property) {
            $context->getNames()->pushName($propertyName);

            $properties[] =
                $this->createProperty(
                    $property,
                    $context,
                    in_array($propertyName, $schema['required'] ?? [], true)
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
                    $protoProp->setScalarType('string');
                    break;
                case 'number':
                    $protoProp->setScalarType('float');
                    break;
                case 'integer':
                    $protoProp->setScalarType('integer');
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
                        ;
                    }
                    break;
                case 'object':
                    $context->getNames()->pushName('dto');

                    $protoProp
                        ->setScalar(false)
                        ->setObjectType($this->createClass($property, $context))
                        ->setScalarType('object')
                    ;

                    $context->getNames()->popName();
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
        $context->getNames()->pushName('dto');

        if (isset($property['type'])) {
            // @todo
            if ($property['type'] !== 'object') {
                throw new \RuntimeException("Non objects not allowed");
            }
            $class = $this->createClass($property, $context);
            $context->getNames()->popName();
            return $class;
        }

        $ref = null;

        if (isset($property['$ref'])) {
            $ref = $context->getSchema()->getRef($property['$ref']);
            $property = $ref->getObject();
        }

        if ($ref !== null && isset($ref->getObject()['type'])) {
            $class =
                $this->createClass(
                    $ref->getObject(),
                    $context->withNames(new Names([$ref->getName()]))
                );
            $context->getNames()->popName();
            return $class;
        }

        if (isset($property['allOf'])) {
            $allProperties = [];
            foreach ($property['allOf'] as $type => $data) {
                if (isset($data['$ref'])) {
                    $object = $this->createObjectProperty($data, $protoProp, $context);
                } else {
                    $context->getNames()->pushName((string) $type);
                    $object = $this->createClass($data, $context);
                    $context->getNames()->popName();
                }
                $allProperties = array_merge($allProperties, $object->getProperties());
            }
            $class =
                $this->createClass(['properties' => []], $context)
                    ->setProperties($allProperties);
            $context->getNames()->popName();
            return $class;
        } elseif (isset($property['oneOf'])) {
            $protoProp->setMultiple(true);
            $init = null;

            foreach ($property['oneOf'] as $type => $data) {
                $context->getNames()->pushName((string) $type);
                if (isset($data['$ref'])) {
                    $object = $this->createObjectProperty($data, $protoProp, $context);
                } else {
                    $object = $this->createClass($data, $context);
                }
                if ($init === null) {
                    $init = $object;
                } else {
                    $protoProp->addOther($object);
                }
                $context->getNames()->popName();
            }
            $context->getNames()->popName();

            return $init;
        } elseif (isset($property['anyOf'])) {
            $protoProp->setMultiple(true);
            $init = null;

            foreach ($property['anyOf'] as $type => $data) {
                $context->getNames()->pushName((string) $type);
                if (isset($data['$ref'])) {
                    $object = $this->createObjectProperty($data, $protoProp, $context);
                } else {
                    $object = $this->createClass($data, $context);
                }
                if ($init === null) {
                    $init = $object;
                } else {
                    $protoProp->addOther($object);
                }
                $context->getNames()->popName();
            }
            $context->getNames()->popName();

            return $init;
        }

        throw new \RuntimeException('Unknown data: '.var_export($property, true));
    }
}