<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

interface TransformerAwareInterface
{
    public function setTransformer(TransformerInterface $transformer): void;
}