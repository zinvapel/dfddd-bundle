<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder\AssertionBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder\PropertyBuilderInterface;

final class Director implements DirectorInterface
{
    private string $name;
    private BuilderInterface $builder;
    private PropertyBuilderInterface $propertyBuilder;
    private AssertionBuilderInterface $assertionBuilder;

    public function __construct(
        string $name,
        BuilderInterface $builder,
        PropertyBuilderInterface $propertyBuilder,
        AssertionBuilderInterface $assertionBuilder
    ) {
        $this->name = $name;
        $this->builder = $builder;
        $this->propertyBuilder = $propertyBuilder;
        $this->assertionBuilder = $assertionBuilder;
    }

    public function build(array $data, BuildContext $context): ?ProtoClassDto
    {
        return
            $this->builder
                ->init($data, $context)
                ->buildName()
                ->buildProperties($this->propertyBuilder)
                ->buildAssertions($this->assertionBuilder)
                ->getProtoClass()
            ;
    }

    public function getName(): string
    {
        return $this->name;
    }
}