<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Enumeration\State;

final class ForbiddenDto implements StatefulDtoInterface
{
    use FailTrait;
}