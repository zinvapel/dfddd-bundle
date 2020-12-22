<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Zinvapel\Enumeration\BaseEnumeration;

abstract class BaseEnumerationType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        return $value !== null ? $this->spawnEnumeration($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return parent::convertToDatabaseValue($value->getValue(), $platform);
    }

    abstract protected function getClassName(): string;

    private function spawnEnumeration($value): BaseEnumeration
    {
        $factory = [$this->getClassName(), 'create'];

        assert(is_callable($factory));

        return $factory($value);
    }
}