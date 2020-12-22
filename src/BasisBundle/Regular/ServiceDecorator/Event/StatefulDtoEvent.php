<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

final class StatefulDtoEvent extends Event
{
    private StatefulDtoInterface $dto;

    public function __construct(StatefulDtoInterface $dto)
    {
        $this->dto = $dto;
    }

    public function getDto(): StatefulDtoInterface
    {
        return $this->dto;
    }
}