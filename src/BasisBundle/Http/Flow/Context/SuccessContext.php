<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow\Context;

use Throwable;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\ExceptionDto;

final class SuccessContext implements ContextInterface
{
    private ServiceDtoInterface $serviceDto;

    public function __construct(ServiceDtoInterface $serviceDto)
    {
        $this->serviceDto = $serviceDto;
    }

    public function apply(ServiceInterface $service): StatefulDtoInterface
    {
        try {
            return $service->perform($this->serviceDto);
        } catch (Throwable $e) {
            return (new ExceptionalContext($e))->apply($service);
        }
    }
}