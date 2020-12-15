<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

interface TransformerInterface
{
    public function transform(ProtoClassDto $protoClassDto): iterable;
}