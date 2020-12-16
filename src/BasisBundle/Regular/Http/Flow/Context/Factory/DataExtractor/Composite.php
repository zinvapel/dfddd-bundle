<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\Http\Flow\Context\Factory\DataExtractor;

use Symfony\Component\HttpFoundation\Request;

class Composite implements DataExtractorInterface
{
    private array $dataExtractorList;

    /**
     * @param DataExtractorInterface[] $dataExtractorList
     */
    public function __construct(array $dataExtractorList)
    {
        $this->dataExtractorList = $dataExtractorList;
    }

    public function extract(Request $request): array
    {
        return
            array_merge(
                ...array_map(
                    fn (DataExtractorInterface $dataExtractor) => $dataExtractor->extract($request),
                    $this->dataExtractorList
                )
            );
    }
}