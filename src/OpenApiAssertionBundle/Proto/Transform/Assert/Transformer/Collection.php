<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

final class Collection implements TransformerInterface, TransformerAwareInterface
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
        return $protoAssert->getName() === 'Collection';
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        yield $this->indent('new Assert\Collection(['.PHP_EOL, $indent);

        foreach ($protoAssert->getOptions() as $name => $option) {
            if (strpos($name, 'proto_') !== 0) {
                yield $this->indent("'$name' => ", $indent+1);
                yield from $this->transformOption($option, $indent+1);
                yield PHP_EOL;
            } else {
                if (is_array($option)) {
                    yield $this->indent("'".substr($name, 6)."' => [".PHP_EOL, $indent+1);

                    foreach ($option as $key => $subProtos) {
                        yield $this->indent("'".$key."' => ", $indent+2);

                        $optional = false;
                        foreach ($subProtos as $index => $subProto) {
                            if ($index === -1) {
                                yield 'new Assert\Optional(['.PHP_EOL;
                                $optional = true;
                            } elseif (!$optional && $index === 0) {
                                yield '['.PHP_EOL;
                            }

                            yield from $this->transformer->transform($subProto, $indent+3);

                            yield ','.PHP_EOL;
                        }

                        if ($optional) {
                            yield $this->indent(']),'.PHP_EOL, $indent+2);
                        } else {
                            yield $this->indent('],'.PHP_EOL, $indent+2);
                        }
                    }
                    yield $this->indent("],".PHP_EOL, $indent+1);
                }
            }
        }

        yield $this->indent('])', $indent);
    }

    private function indent(string $string, int $indent): string
    {
        return str_repeat('    ', $indent).$string;
    }

    private function transformOption($option, int $indent): iterable
    {
        switch (gettype($option)) {
            case 'boolean':
                yield $option ? 'true' : 'false';
                break;
            case 'integer':
            case 'double':
                yield (string) $option;
                break;
            case 'string':
                yield "'".((string) $option)."'";
                break;
            case 'array':
                yield '['.PHP_EOL;

                foreach ($option as $key => $value) {
                    if (is_string($key)) {
                        yield $this->indent("'$key' => ", $indent+1);
                    }

                    yield from $this->transformOption($value, $indent+1);
                }

                yield $this->indent(']', $indent);
        }

        yield ',';
    }
}