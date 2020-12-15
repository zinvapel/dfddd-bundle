<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Core\Dto;

use Zinvapel\Basis\BasisBundle\Core\Enumeration\State;

interface StatefulDtoInterface
{
    public function getState(): State;
}