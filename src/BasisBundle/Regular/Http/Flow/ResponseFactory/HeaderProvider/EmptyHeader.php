<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\HeaderProvider;

use Symfony\Component\HttpFoundation\Request;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

final class EmptyHeader implements HeaderProviderInterface
{
    public function provide(StatefulDtoInterface $statefulDto, Request $request): array
    {
        return [];
    }
}