<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\ResponseFactory\ResponseFactoryInterface;

final class Predefined implements ResponseFactoryInterface
{
    private int $statusCode;
    private string $content;

    public function __construct(int $statusCode, string $content = '')
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
    }

    public function createFromDto(StatefulDtoInterface $resultDto, Request $request): Response
    {
        return new Response($this->content, $this->statusCode);
    }
}