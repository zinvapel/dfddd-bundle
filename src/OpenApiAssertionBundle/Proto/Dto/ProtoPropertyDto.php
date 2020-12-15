<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto;

final class ProtoPropertyDto
{
    private string $name;
    private string $scalarType;
    private ?ProtoClassDto $objectType = null;
    private bool $scalar;
    private bool $nullable;
    private bool $collection;
    private bool $multiple = false;
    private array $others = [];
    private Artifact $artifact;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getScalarType(): string
    {
        return $this->scalarType;
    }

    public function setScalarType(string $scalarType): self
    {
        $this->scalarType = $scalarType;

        return $this;
    }

    public function getObjectType(): ?ProtoClassDto
    {
        return $this->objectType;
    }

    public function setObjectType(?ProtoClassDto $objectType): self
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function isScalar(): bool
    {
        return $this->scalar;
    }

    public function setScalar(bool $scalar): self
    {
        $this->scalar = $scalar;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function isCollection(): bool
    {
        return $this->collection;
    }

    public function setCollection(bool $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getArtifact(): Artifact
    {
        return $this->artifact;
    }

    public function setArtifact(Artifact $artifact): self
    {
        $this->artifact = $artifact;

        return $this;
    }

    public function getOthers(): array
    {
        return $this->others;
    }

    public function setOthers(array $others): self
    {
        $this->others = $others;

        return $this;
    }

    public function addOther(ProtoClassDto $other): self
    {
        $this->others[] = $other;

        return $this;
    }
}