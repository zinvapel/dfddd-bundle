<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

interface TransformerInterface
{
    public function isAccept(ProtoAssertDto $protoAssert): bool;
    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable;
}