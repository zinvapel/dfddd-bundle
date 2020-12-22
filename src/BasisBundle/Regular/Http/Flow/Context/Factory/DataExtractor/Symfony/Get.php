<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Symfony;

use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Composite;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Query;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\RouteParameters;

final class Get extends Composite
{
    public function __construct(Query $query, RouteParameters $routeParameters)
    {
        parent::__construct([$query, $routeParameters]);
    }
}