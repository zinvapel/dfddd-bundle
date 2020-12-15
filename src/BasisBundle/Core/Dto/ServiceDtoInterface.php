<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Core\Dto;

use Symfony\Component\Validator\Constraint;

interface ServiceDtoInterface
{
    public static function getConstraints(): Constraint;
}