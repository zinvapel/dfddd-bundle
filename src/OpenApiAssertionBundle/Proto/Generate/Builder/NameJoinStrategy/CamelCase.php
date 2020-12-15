<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy;

final class CamelCase implements JoinStrategyInterface
{
    public function join(array $names): string
    {
        return implode('', array_map('ucfirst', $names));
    }
}