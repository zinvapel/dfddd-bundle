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
        $env = getenv('ZINVAPEL_BASIS_HTTP_FLOW_DEBUG');

        return
            new ExceptionDto(
                $this->exception,
                $env !== false || $this->exception instanceof PublicExceptionInterface
            );
    }
}