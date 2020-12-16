<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\HeaderProvider;

use Symfony\Component\HttpFoundation\Request;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

final class StaticKeyValue implements HeaderProviderInterface
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function provide(StatefulDtoInterface $statefulDto, Request $request): array
    {
        return [$this->name => $this->value];
    }
}