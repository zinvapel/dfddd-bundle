<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\ResponseFactory\ResponseFactoryInterface;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\ResponseFactory\HeaderProvider\HeaderProviderInterface;

final class Serialize implements ResponseFactoryInterface
{
    private SerializerInterface $serializer;
    private HeaderProviderInterface $headerProvider;
    private array $context;
    private int $statusCode;
    private string $format;

    public function __construct(
        SerializerInterface $serializer,
        HeaderProviderInterface $headerProvider,
        string $format,
        int $statusCode = Response::HTTP_OK,
        array $context = []
    ) {
        $this->serializer = $serializer;
        $this->headerProvider = $headerProvider;
        $this->statusCode = $statusCode;
        $this->context = $context;
        $this->format = $format;
    }

    public function createFromDto(StatefulDtoInterface $dto, Request $request): Response
    {
        return
            new Response(
                $this->serializer->serialize($dto, $this->format, $this->context),
                $this->statusCode,
                $this->headerProvider->provide($dto, $request)
            );
    }
}