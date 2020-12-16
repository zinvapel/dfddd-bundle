<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Enumeration\State;
use Symfony\Component\Serializer\Annotation as Serializer;

final class InvalidDto implements StatefulDtoInterface
{
    /**
     * @Serializer\Groups({"body"})
     */
    private array $violations = [];

    public function getState(): State
    {
        return State::fail();
    }

    public function getViolations(): array
    {
        return $this->violations;
    }

    public function setViolations(array $violations): self
    {
        $this->violations = $violations;

        return $this;
    }
}