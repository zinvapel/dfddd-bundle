<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto;

final class Artifact
{
    private array $data;
    private array $groups;

    public function __construct(array $data, array $groups)
    {
        $this->data = $data;
        $this->groups = $groups;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}