<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Dto;

final class Ref
{
    private string $name;
    private array $object;

    public function __construct(string $name, array $object)
    {
        $this->name = $name;
        $this->object = $object;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getObject(): array
    {
        return $this->object;
    }
}