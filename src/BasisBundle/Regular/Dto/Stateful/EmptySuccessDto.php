<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

final class EmptySuccessDto implements StatefulDtoInterface
{
    use SuccessTrait;
}