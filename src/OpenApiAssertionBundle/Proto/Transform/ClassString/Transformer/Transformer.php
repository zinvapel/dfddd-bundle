<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Transformer;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoPropertyDto;

final class Transformer implements TransformerInterface
{
    public function transform(ProtoClassDto $protoClassDto): iterable
    {
        yield 'final class '.$protoClassDto->getName().PHP_EOL;
        yield '{'.PHP_EOL;

        foreach ($protoClassDto->getProperties() as $protoPropertyDto) {
            yield from $this->createPhpDoc($protoPropertyDto);
            yield from $this->createProperty($protoPropertyDto);
            yield PHP_EOL;
        }

        foreach ($protoClassDto->getProperties() as $protoPropertyDto) {
            yield from $this->createMethods($protoPropertyDto);
        }

        yield '}'.PHP_EOL;
    }

    private function createPhpDoc(ProtoPropertyDto $protoPropertyDto): iterable
    {
        if ($protoPropertyDto->getArtifact()->getGroups()) {
            yield "        /**".PHP_EOL;
            yield "         * @Serializer\Groups({";
            foreach ($protoPropertyDto->getArtifact()->getGroups() as $group) {
                yield "\"$group\"";
            }
            yield "})".PHP_EOL;
            yield from $this->createVarDoc($protoPropertyDto);
            yield "         */".PHP_EOL;
        } else {
            if ($protoPropertyDto->isCollection()) {
                yield "        /**".PHP_EOL;
                yield from $this->createVarDoc($protoPropertyDto);
                yield "         */".PHP_EOL;
            }
        }
    }

    private function createVarDoc(ProtoPropertyDto $protoPropertyDto): iterable
    {
        if ($protoPropertyDto->isCollection()) {
            if ($protoPropertyDto->isScalar()) {
                yield "         * @var ".$protoPropertyDto->getScalarType()."[]";

                if ($protoPropertyDto->isNullable()) {
                    yield "|null";
                }
            } else {
                yield "         * @var ".$protoPropertyDto->getObjectType()->getName()."[]";

                if ($protoPropertyDto->isMultiple()) {
                    foreach ($protoPropertyDto->getOthers() as $other) {
                        yield "|".$other->getName()."[]";
                    }
                }

                if ($protoPropertyDto->isNullable()) {
                    yield "|null";
                }
            }

            yield " ".$protoPropertyDto->getName().PHP_EOL;
        }
    }

    private function createProperty(ProtoPropertyDto $protoPropertyDto): iterable
    {
        yield "        private ";

        if ($protoPropertyDto->isNullable()) {
            yield '?';
        }

        if ($protoPropertyDto->isCollection()) {
            yield 'array ';
        } else {
            if ($protoPropertyDto->isScalar()) {
                yield $protoPropertyDto->getScalarType()." ";
            } else {
                if (!$protoPropertyDto->isMultiple()) {
                    yield $protoPropertyDto->getObjectType()->getName()." ";
                }
            }
        }

        yield '$'.lcfirst($protoPropertyDto->getName()).";".PHP_EOL;
    }

    private function createMethods(ProtoPropertyDto $protoPropertyDto): iterable
    {
        yield "        public function set".ucfirst($protoPropertyDto->getName())."(";

        if ($protoPropertyDto->isNullable()) {
            yield '?';
        }

        if ($protoPropertyDto->isCollection()) {
            yield 'array ';
        } else {
            if ($protoPropertyDto->isScalar()) {
                yield $protoPropertyDto->getScalarType()." ";
            } else {
                if (!$protoPropertyDto->isMultiple()) {
                    yield $protoPropertyDto->getObjectType()->getName()." ";
                }
            }
        }
        $propName = lcfirst($protoPropertyDto->getName());

        yield '$'.$propName;
        if ($protoPropertyDto->isNullable()) {
            yield ' = null';
        }
        yield "): self".PHP_EOL;
        yield "        {".PHP_EOL;
        yield "                \$this->".$propName." = ".$propName.";".PHP_EOL;
        yield PHP_EOL;
        yield "                return \$this;".PHP_EOL;
        yield "        }".PHP_EOL;


        yield PHP_EOL;
        if ($protoPropertyDto->getScalarType() !== 'bool') {
            yield "        public function get".ucfirst($protoPropertyDto->getName())."()";
        } else {
            yield "        public function ".lcfirst($protoPropertyDto->getName())."()";
        }

        if (!$protoPropertyDto->isMultiple()) {
            yield ': ';
        }

        if ($protoPropertyDto->isNullable()) {
            yield '?';
        }

        if ($protoPropertyDto->isCollection()) {
            yield 'array';
        } else {
            if ($protoPropertyDto->isScalar()) {
                yield $protoPropertyDto->getScalarType();
            } else {
                if (!$protoPropertyDto->isMultiple()) {
                    yield $protoPropertyDto->getObjectType()->getName();
                }
            }
        }

        yield PHP_EOL;
        yield "        {".PHP_EOL;
        yield "                return \$this->".$propName.";".PHP_EOL;
        yield "        }".PHP_EOL;

        yield PHP_EOL;
    }
}