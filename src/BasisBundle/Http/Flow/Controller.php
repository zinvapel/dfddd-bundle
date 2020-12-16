<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\Factory\ContextFactoryInterface;
use Zinvapel\Basis\BasisBundle\Http\Flow\ResponseFactory\ResponseFactoryInterface;

final class Controller
{
    private ContextFactoryInterface $contextFactory;
    private ServiceInterface $service;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        ContextFactoryInterface $contextFactory,
        ServiceInterface $service,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->contextFactory = $contextFactory;
        $this->service = $service;
        $this->responseFactory = $responseFactory;
    }

    public function handleRequest(Request $request): Response
    {
        $context = $this->contextFactory->createFromRequest($request);

        $resultDto = $context->apply($this->service);

        return $this->responseFactory->createFromDto($resultDto, $request);
    }
}