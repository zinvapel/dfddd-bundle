<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow\Context\Factory;

use Symfony\Component\HttpFoundation\Request;
use Zinvapel\Basis\BasisBundle\Http\Flow\Context\ContextInterface;

interface ContextFactoryInterface
{
    public function createFromRequest(Request $request): ContextInterface;
}