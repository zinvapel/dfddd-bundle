<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Enumeration;

use Zinvapel\Enumeration\BaseEnumeration;

final class Target extends BaseEnumeration
{
    public const FULL = 'full';
    public const OBJECT = 'object';
    public const HTTP = 'http';
    public const ROUTE = 'route';

    protected array $names = [
        self::FULL => 'All classes from context',
        self::OBJECT => 'Concrete object',
        self::HTTP => 'Requests and responses',
        self::ROUTE => 'All for single route',
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

    public static function route(): self
    {
        return new self(self::ROUTE);
    }

    public function isObjectable(): bool
    {
        return in_array($this->getValue(), [self::OBJECT, self::ROUTE]);
    }
}