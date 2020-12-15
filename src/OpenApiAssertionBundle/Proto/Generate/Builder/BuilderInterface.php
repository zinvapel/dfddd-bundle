<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder\AssertionBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy\JoinStrategyInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder\PropertyBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

interface BuilderInterface
{
    public function init(array $data, BuildContext $context): BuilderInterface;
    public function buildName(): BuilderInterface;
    public function buildProperties(PropertyBuilderInterface $propertyBuilder): BuilderInterface;
    public function buildAssertions(AssertionBuilderInterface $assertionBuilder): BuilderInterface;
    public function getProtoClass(): ?ProtoClassDto;
}