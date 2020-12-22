<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Service;

use Symfony\Component\Validator\Constraint;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\Validatable;

final class EmptyDto implements ServiceDtoInterface, Validatable
{
    public static function getConstraints(): array
    {
        return [];
    }
}