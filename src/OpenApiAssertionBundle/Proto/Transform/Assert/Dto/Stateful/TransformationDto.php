<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\SuccessTrait;

final class TransformationDto implements StatefulDtoInterface
{
    use SuccessTrait;

    private array $assertion;

    public function getAssertion(): array
    {
        return $this->assertion;
    }

    public function setAssertion(array $assertion): self
    {
        $this->assertion = $assertion;

        return $this;
    }
}