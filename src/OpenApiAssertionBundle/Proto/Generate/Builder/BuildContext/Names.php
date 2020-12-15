<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy\JoinStrategyInterface;

final class Names
{
    private array $names;

    public function __construct(array $names)
    {
        $this->names = $names;
    }

    public function pushName(string $name): self
    {
        $this->names[] = $name;

        return $this;
    }

    public function popName(): ?string
    {
        return array_pop($this->names);
    }

    public function getName(): ?string
    {
        $res = end($this->names);

        return $res === false ? null : $res;
    }

    public function joinNames(JoinStrategyInterface $joinStrategy): string
    {
        return $joinStrategy->join($this->names);
    }
}