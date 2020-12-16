<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Symfony\Component\Serializer\Annotation as Serializer;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

final class NotFoundDto implements StatefulDtoInterface
{
    use FailTrait;

    /**
     * @Serializer\Groups({"body"})
     */
    private string $message;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}