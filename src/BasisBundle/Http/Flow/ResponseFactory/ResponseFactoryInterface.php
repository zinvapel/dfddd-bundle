<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;

interface ResponseFactoryInterface
{
    public function createFromDto(StatefulDtoInterface $resultDto, Request $request): Response;
}