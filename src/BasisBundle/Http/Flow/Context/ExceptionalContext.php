<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow\Context;

use Throwable;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Exception\PublicExceptionInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\ExceptionDto;

final class ExceptionalContext implements ContextInterface
{
    private Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function apply(ServiceInterface $service): StatefulDtoInterface
    {
        return
            new ExceptionDto(
                $this->exception,
                isset($_SERVER['ZINVAPEL_BASIS_HTTP_FLOW_DEBUG']) || $this->exception instanceof PublicExceptionInterface
            );
    }
}