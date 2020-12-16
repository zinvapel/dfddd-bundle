<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Symfony;

use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Composite;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\JsonBody;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\RouteParameters;

final class PostJson extends Composite
{
    public function __construct(RouteParameters $routeParameters, JsonBody $jsonBody)
    {
        parent::__construct([$routeParameters, $jsonBody]);
    }
}