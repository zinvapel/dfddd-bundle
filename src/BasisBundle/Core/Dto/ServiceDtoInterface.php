<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Core\Dto;

use Symfony\Component\Validator\Constraint;

interface ServiceDtoInterface
{
    /**
     * @return Constraint[]
     */
    public static function getConstraints(): array;
}