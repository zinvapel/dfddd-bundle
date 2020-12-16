<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor;

use Symfony\Component\HttpFoundation\Request;
use Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor\Exception\InvalidBodyException;

final class JsonBody implements DataExtractorInterface
{
    public function extract(Request $request): array
    {
        try {
            $data = json_decode($request->getContent(), true, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidBodyException('Body is not a array or object JSON');
        }

        return $data;
    }
}