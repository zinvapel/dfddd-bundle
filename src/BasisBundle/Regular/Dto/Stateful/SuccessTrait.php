<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Enumeration\State;

trait SuccessTrait
{
    public function getState(): State
    {
        return State::create(State::SUCCESS);
    }
}