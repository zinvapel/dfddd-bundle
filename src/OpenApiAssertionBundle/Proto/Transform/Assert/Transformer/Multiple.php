<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;
use function Symfony\Component\VarDumper\Dumper\esc;

final class Multiple implements TransformerInterface, TransformerAwareInterface
{
    private TransformerInterface $transformer;

    public function __construct()
    {
        $this->transformer = new None();
    }

    public function setTransformer(TransformerInterface $transformer): void
    {
        $this->transformer = $transformer;
    }

    public function isAccept(ProtoAssertDto $protoAssert): bool
    {
        return in_array($protoAssert->getName(), ['All', 'AtLeastOneOf'], true);
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        yield $this->indent('new Assert\\'.$protoAssert->getName().'(['.PHP_EOL, $indent);

        foreach ($protoAssert->getOptions()['proto_constraints'] as $name => $option) {
            if (is_array($option)) {
                foreach ($option as $subAssert) {
                    yield from $this->transformer->transform($subAssert, $indent+1);
                }
            } else {
                yield from $this->transformer->transform($option, $indent+1);
            }
            yield ','.PHP_EOL;
        }

        yield $this->indent('])', $indent);
    }

    private function indent(string $string, int $indent): string
    {
        return str_repeat('    ', $indent).$string;
    }
}