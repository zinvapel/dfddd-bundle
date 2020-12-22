<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor;

use Symfony\Component\HttpFoundation\Request;

final class RouteParameters implements DataExtractorInterface
{
    public function extract(Request $request): array
    {
        $data = [];

        foreach ($request->attributes->get('_route_params', []) as $key => $value) {
            if (strpos(strtolower($key), 'id') !== false) {
                $value = (int) $value;
            }

            $data[$key] = (int) $value;
        }

        return $data;
    }
}