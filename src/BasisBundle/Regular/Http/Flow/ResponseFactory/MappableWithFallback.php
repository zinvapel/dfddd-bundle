<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\ResponseFactory\ResponseFactoryInterface;

final class MappableWithFallback implements ResponseFactoryInterface
{
    private array $map;
    private ResponseFactoryInterface $fallback;

    public function __construct(array $map, ResponseFactoryInterface $fallback)
    {
        $this->map = $map;
        $this->fallback = $fallback;
    }

    public function createFromDto(StatefulDtoInterface $resultDto, Request $request): Response
    {
        if (isset($this->map[get_class($resultDto)])) {
            return $this->map[get_class($resultDto)]->createFromDto($resultDto, $request);
        }

        return $this->fallback->createFromDto($resultDto, $request);
    }
}