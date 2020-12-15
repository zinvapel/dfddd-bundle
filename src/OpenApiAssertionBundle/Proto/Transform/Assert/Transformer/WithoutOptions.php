<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

final class WithoutOptions implements TransformerInterface
{
    public function isAccept(ProtoAssertDto $protoAssert): bool
    {
        return in_array($protoAssert->getName(), ['NotBlank', 'NotNull'], true);
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        yield $this->indent('new Assert\\'.$protoAssert->getName().'()', $indent);
    }

    private function indent(string $string, int $indent): string
    {
        return str_repeat('    ', $indent).$string;
    }
}