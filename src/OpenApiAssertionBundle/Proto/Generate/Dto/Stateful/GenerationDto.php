<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Stateful;

use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\SuccessTrait;

final class GenerationDto implements StatefulDtoInterface
{
    use SuccessTrait;

    private array $protoMaps = [];

    public function getProtoMaps(): array
    {
        return $this->protoMaps;
    }

    public function setProtoMaps(array $protoMaps): self
    {
        $this->protoMaps = $protoMaps;

        return $this;
    }
}