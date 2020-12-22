<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Symfony\Component\Serializer\Annotation as Serializer;
use Throwable;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Enumeration\State;

final class ExceptionDto implements StatefulDtoInterface
{
    private Throwable $exception;
    private bool $display;

    public function __construct(Throwable $exception, bool $display)
    {
        $this->exception = $exception;
        $this->display = $display;
    }

    public function getState(): State
    {
        return State::exception();
    }

    /**
     * @Serializer\Groups({"body"})
     */
    public function getData(): array
    {
        return $this->display
            ? [
                'exception' => get_class($this->exception),
                'message' => $this->exception->getMessage(),
                'trace' => $this->exception->getTraceAsString(),
            ]
            : [];
    }
}