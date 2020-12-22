<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoAssertDto;

final class WithOptions implements TransformerInterface
{
    private const NAMES = [
        'Type',
        'Choice',
        'Regex',
        'Length',
        'GreaterThan',
        'GreaterThanOrEqual',
        'LessThanOrEqual',
        'LessThan',
        'DateTime',
    ];

    public function isAccept(ProtoAssertDto $protoAssert): bool
    {
        return in_array($protoAssert->getName(), self::NAMES, true);
    }

    public function transform(ProtoAssertDto $protoAssert, int $indent): iterable
    {
        yield $this->indent('new Assert\\'.$protoAssert->getName().'(['.PHP_EOL, $indent);

        foreach ($protoAssert->getOptions() as $name => $option) {
            if (is_string($name)) {
                yield $this->indent("'$name' => ", $indent+1);
            }

            yield from $this->castToString($option, $indent+1);

            yield ','.PHP_EOL;
        }

        yield $this->indent('])', $indent);
    }

    private function indent(string $string, int $indent): string
    {
        return str_repeat('    ', $indent).$string;
    }

    private function castToString($option, int $indent): iterable
    {
        switch (gettype($option)) {
            case 'string':
                if (strpos((string) $option, '\DateTime') === 0) {
                    yield $option;
                } else {
                    yield "'$option'";
                }
                break;
            case 'boolean':
                yield $option ? 'true' : 'false';
                break;
            case 'array':
                yield '[';

                foreach ($option as $k => $v) {
                    if (is_string($k)) {
                        yield $this->indent("'$k' => ", $indent+1);
                    }

                    yield from $this->castToString($v, $indent+1);
                    yield ',';
                }
                yield ']';

                break;
            default:
                yield $option;
        }
    }
}