<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy;

interface JoinStrategyInterface
{
    public function join(array $names): string;
}