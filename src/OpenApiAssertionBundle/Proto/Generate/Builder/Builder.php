<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder\AssertionBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy\JoinStrategyInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder\PropertyBuilderInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

final class Builder implements BuilderInterface
{
    private ?array $data = null;
    private ?BuildContext $context = null;
    private ?ProtoClassDto $proto;

    public function init(array $data, BuildContext $context): BuilderInterface
    {
        $this->data = $data;
        $this->context = $context;
        $this->proto = new ProtoClassDto();

        return $this;
    }

    public function buildName(): BuilderInterface
    {
        if (in_array(null, [$this->data, $this->context, $this->proto], true)) {
            return new None();
        }

        $name = $this->context->getNames()->joinNames($this->context->getJoinStrategy());
        $this->proto->setName($name);
        $this->context->getKnownObjects()->set($name, $this->proto);

        return $this;
    }

    public function buildProperties(PropertyBuilderInterface $propertyBuilder): BuilderInterface
    {
        if (in_array(null, [$this->data, $this->context, $this->proto], true)) {
            return new None();
        }

        $propertyBuilder->build($this->proto, $this->context, $this->data);

        return $this;
    }

    public function buildAssertions(AssertionBuilderInterface $assertionBuilder): BuilderInterface
    {
        if (in_array(null, [$this->data, $this->context, $this->proto], true)) {
            return new None();
        }

        $assertionBuilder->build($this->proto, $this->context, $this->data);

        foreach ($this->context->getKnownObjects()->toArray() as $value) {
            if (count($value->getAssertions()) === 0) {
                $assertionBuilder->build($value, $this->context, $this->data);
            }
        }

        return $this;
    }

    public function getProtoClass(): ?ProtoClassDto
    {
        return $this->proto;
    }
}