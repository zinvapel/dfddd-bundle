<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Service;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;

final class EmptyDto implements ServiceDtoInterface
{
    public static function getConstraints(): Constraint
    {
        return new Assert\Valid();
    }
}