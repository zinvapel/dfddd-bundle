<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

final class FirstAcceptable implements TransformerInterface
{
    private array $transformerList;

    /**
     * @param TransformerInterface[] $transformerList
     */
    public function __construct(array $transformerList)
    {
        foreach ($transformerList as $transformer) {
            if ($transformer instanceof TransformerAwareInterface) {
                $transformer->setTransformer($this);
            }
        }

        $this->transformerList = $transformerList;
    }

    public function isAccept(ProtoAssertDto $protoAssert): bool
    {
        return true;
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        foreach ($this->transformerList as $transformer) {
            if ($transformer->isAccept($protoAssert)) {
                yield from $transformer->transform($protoAssert, $indent);
            }
        }
    }

}