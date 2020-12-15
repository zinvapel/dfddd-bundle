<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Core\Enumeration;

use Zinvapel\Enumeration\BaseEnumeration;

final class State extends BaseEnumeration
{
    public const SUCCESS = 'success';
    public const FAIL = 'fail';
    public const EXCEPTION = 'exceptional';

    protected $names = [
        self::SUCCESS => 'Success',
        self::FAIL => 'Fail',
        self::EXCEPTION => 'Exception',
    ];

    public static function success(): self
    {
        return self::create(self::SUCCESS);
    }

    public static function fail(): self
    {
        return self::create(self::FAIL);
    }

    public static function exception(): self
    {
        return self::create(self::EXCEPTION);
    }

    public function isSuccess(): bool
    {
        return $this->eq(self::SUCCESS);
    }

    public function isFail(): bool
    {
        return $this->eq(self::FAIL);
    }

    public function isException(): bool
    {
        return $this->eq(self::EXCEPTION);
    }
}