<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator\Event\StatefulDtoEvent;

final class EventDispatch implements ServiceInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private ServiceInterface $inner;

    public function __construct(EventDispatcherInterface $eventDispatcher, ServiceInterface $inner)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->inner = $inner;
    }

    public function perform(ServiceDtoInterface $dto): StatefulDtoInterface
    {
        $result = $this->inner->perform($dto);

        if ($result->getState()->isSuccess()) {
            $this->eventDispatcher->dispatch(new StatefulDtoEvent($result), get_class($result));
        }

        return $result;
    }
}