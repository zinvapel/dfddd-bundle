<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

final class RefObject implements TransformerInterface
{
    public function isAccept(ProtoAssertDto $protoAssert): bool
    {
        return $protoAssert->getName() === 'RefObject';
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        yield $this->indent($protoAssert->getOptions()['name']."::asConstraint()", $indent);
    }

    private function indent(string $string, int $indent): string
    {
        return str_repeat('    ', $indent).$string;
    }
}