<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor;

use Symfony\Component\HttpFoundation\Request;

final class Query implements DataExtractorInterface
{
    public function extract(Request $request): array
    {
        $parameters = $request->query->all();

        foreach ($parameters as $name => $param) {
            if (in_array($name, ['limit', 'offset'])) {
                $parameters[$name] = (int) $param;
            }
        }

        return $parameters;
    }
}