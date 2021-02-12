<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\FailTrait;
use Symfony\Component\Serializer\Annotation as Serializer;

final class FailedDto implements StatefulDtoInterface
{
    use FailTrait;

    /**
     * @Serializer\Groups({"body"})
     */
    private string $reason;

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}