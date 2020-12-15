<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoPropertyDto;

final class AssertionBuilder implements AssertionBuilderInterface
{
    public function build(ProtoClassDto $proto, BuildContext $context, array $data): void
    {
        $assertions = [];

        foreach ($proto->getProperties() as $protoProperty) {
            $assertions[$protoProperty->getName()] = $this->createAsserts($protoProperty, $context);

            if ($protoProperty->isMultiple()) {
                $assertions[$protoProperty->getName()] = [
                    (new ProtoAssertDto())
                        ->setName('AtLeastOneOf')
                        ->setOptions(
                            [
                                'proto_constraints' =>
                                    array_merge(
                                        [
                                            (function () use ($protoProperty, $context) {
                                                $this->build($protoProperty->getObjectType(), $context, []);

                                                return $protoProperty->getObjectType()->getAssertions();
                                            })()
                                        ],
                                        array_map(
                                            function (ProtoClassDto $other) use ($context) {
                                                $this->build($other, $context, []);

                                                return $other->getAssertions();
                                            },
                                            $protoProperty->getOthers()
                                        )
                                    )
                            ]
                        )
                ];
            }
        }

        $proto->setAssertions([
            (new ProtoAssertDto())
                ->setName('Collection')
                ->setOptions(['allowExtraFields' => true, 'proto_fields' => $assertions])
        ]);
    }

    /**
     * @param ProtoPropertyDto $protoProperty
     * @param BuildContext $context
     * @return ProtoAssertDto[]
     */
    private function createAsserts(ProtoPropertyDto $protoProperty, BuildContext $context): array
    {
        $data = $protoProperty->getArtifact()->getData();
        $asserts = $protoProperty->isNullable() ? [] : [-1 => (new ProtoAssertDto())->setName('NotNull')];

        if ($protoProperty->isCollection()) {
            $assert = new ProtoAssertDto();
            $assert->setName('All');
            $options = [
                'proto_constraints' =>
                    $this->createAsserts(
                        (clone $protoProperty)->setCollection(false),
                        $context
                    )
            ];

            if (isset($data['minItems'])) {
                $asserts[] =
                    (new ProtoAssertDto())
                        ->setName('Length')
                        ->setOptions(['min' => $data['minItems']])
                    ;
            }

            if (isset($data['maxItems'])) {
                $asserts[] =
                    (new ProtoAssertDto())
                        ->setName('Length')
                        ->setOptions(['max' => $data['maxItems']])
                ;
            }

            $assert->setOptions($options);
            $asserts[] = $assert;
        } else {
            switch ($protoProperty->getScalarType()) {
                case 'string':
                    $asserts[] = (new ProtoAssertDto())->setName('NotBlank');
                    $asserts[] =
                        (new ProtoAssertDto())
                            ->setName('Type')
                            ->setOptions(['type' => $protoProperty->getScalarType()])
                        ;

                    if (isset($data['enum'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName('Choice')
                                ->setOptions([
                                    'choices' => array_map('strval', $data['enum']),
                                ]);
                    }

                    if (isset($data['pattern'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName('Regex')
                                ->setOptions([
                                    'pattern' => $data['pattern'],
                                ]);
                    }

                    if (isset($data['minLength'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName('Length')
                                ->setOptions(['min' => $data['minLength']])
                        ;
                    }

                    if (isset($data['maxLength'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName('Length')
                                ->setOptions(['max' => $data['maxLength']])
                        ;
                    }
                    // @todo implement format
                    break;
                case 'integer':
                case 'float':
                    $asserts[] =
                        (new ProtoAssertDto())
                            ->setName('Type')
                            ->setOptions(['type' => $protoProperty->getScalarType()])
                    ;

                    if (isset($data['minimum'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName(
                                    isset($data['exclusiveMinimum']) && $data['exclusiveMinimum']
                                        ? 'GreaterThan' : 'GreaterThanOrEqual'
                                )
                                ->setOptions(['value' => $data['minimum']])
                            ;
                    }
                    if (isset($data['maximum'])) {
                        $asserts[] =
                            (new ProtoAssertDto())
                                ->setName(
                                    isset($data['exclusiveMaximum']) && $data['exclusiveMaximum']
                                        ? 'LessThan' : 'LessThanOrEqual'
                                )
                                ->setOptions(['value' => $data['maximum']])
                            ;
                    }
                    break;
                case 'bool':
                    $asserts[] =
                        (new ProtoAssertDto())
                            ->setName('Type')
                            ->setOptions(['type' => $protoProperty->getScalarType()])
                    ;
                    break;
                case 'object':
                    $options = ['name' => $protoProperty->getObjectType()->getName()];

                    if (count($protoProperty->getObjectType()->getAssertions()) === 0) {
                        $this->build($protoProperty->getObjectType(), $context, []);
                    }

                    $asserts =
                        array_merge(
                            $protoProperty->getObjectType()->getAssertions(),
                            $asserts
                        );

//                    $asserts[] =
//                        (new ProtoAssertDto())
//                            ->setName('RefObject')
//                            ->setOptions($options)
//                        ;
            }
        }

        return $asserts;
    }
}