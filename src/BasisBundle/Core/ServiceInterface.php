<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Core;

use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

interface ServiceInterface
{
    /**
     * @param ServiceDtoInterface $dto
     * @return StatefulDtoInterface
     */
    public function perform(ServiceDtoInterface $dto): StatefulDtoInterface;
}