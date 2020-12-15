<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\SuccessTrait;

final class TransformationDto implements StatefulDtoInterface
{
    use SuccessTrait;

    /**
     * @var string[]
     */
    private array $classes = [];

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function setClasses(array $classes): self
    {
        $this->classes = $classes;

        return $this;
    }
}