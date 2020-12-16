<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Enumeration;

use Zinvapel\Enumeration\BaseEnumeration;

final class Target extends BaseEnumeration
{
    public const FULL = 'full';
    public const OBJECT = 'object';
    public const HTTP = 'http';

    protected array $names = [
        self::FULL => 'All classes from context',
        self::OBJECT => 'Concrete object',
        self::HTTP => 'Requests and responses',
    ];

    public static function full(): self
    {
        return new self(self::FULL);
    }

    public static function object(): self
    {
        return new self(self::OBJECT);
    }

    public static function http(): self
    {
        return new self(self::HTTP);
    }
}