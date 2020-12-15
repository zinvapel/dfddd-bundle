<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto;

final class ProtoClassDto
{
    private string $name;

    /**
     * @var ProtoPropertyDto[]
     */
    private array $properties = [];

    /**
     * @var ProtoAssertDto[]
     */
    private array $assertions = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getAssertions(): array
    {
        return $this->assertions;
    }

    public function setAssertions(array $assertions): self
    {
        $this->assertions = $assertions;

        return $this;
    }
}