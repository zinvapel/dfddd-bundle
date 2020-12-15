<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder\AssertionBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder\PropertyBuilderInterface;

final class None implements BuilderInterface
{
    public function init(array $data, BuildContext $context): BuilderInterface
    {
        return $this;
    }

    public function buildName(): BuilderInterface
    {
        return $this;
    }

    public function buildProperties(PropertyBuilderInterface $propertyBuilder): BuilderInterface
    {
        return $this;
    }

    public function buildAssertions(AssertionBuilderInterface $assertionBuilder): BuilderInterface
    {
        return $this;
    }

    public function getProtoClass(): ?ProtoClassDto
    {
        return null;
    }
}