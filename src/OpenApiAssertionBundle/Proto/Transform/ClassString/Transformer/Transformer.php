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
            yield "\t/**".PHP_EOL;
            yield "\t * @Serializer\Groups({";
            foreach ($protoPropertyDto->getArtifact()->getGroups() as $group) {
                yield "\"$group\"";
            }
            yield "})".PHP_EOL;
            yield from $this->createVarDoc($protoPropertyDto);
            yield "\t */".PHP_EOL;
        } else {
            if ($protoPropertyDto->isCollection()) {
                yield "\t/**".PHP_EOL;
                yield from $this->createVarDoc($protoPropertyDto);
                yield "\t */".PHP_EOL;
            }
        }
    }

    private function createVarDoc(ProtoPropertyDto $protoPropertyDto): iterable
    {
        if ($protoPropertyDto->isCollection()) {
            if ($protoPropertyDto->isScalar()) {
                yield "\t * @var ".$protoPropertyDto->getScalarType()."[]";

                if ($protoPropertyDto->isNullable()) {
                    yield "|null";
                }
            } else {
                yield "\t * @var ".$protoPropertyDto->getObjectType()->getName()."[]";

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
        yield "\tprivate ";

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
        yield "\tpublic function set".ucfirst($protoPropertyDto->getName())."(";

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
        yield "\t{".PHP_EOL;
        yield "\t\t\$this->".$propName." = ".$propName.";".PHP_EOL;
        yield PHP_EOL;
        yield "\t\treturn \$this;".PHP_EOL;
        yield "\t}".PHP_EOL;


        yield PHP_EOL;
        if ($protoPropertyDto->getScalarType() !== 'bool') {
            yield "\tpublic function get".ucfirst($protoPropertyDto->getName())."()";
        } else {
            yield "\tpublic function ".lcfirst($protoPropertyDto->getName())."()";
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
        yield "\t{".PHP_EOL;
        yield "\t\treturn \$this->".$propName.";".PHP_EOL;
        yield "\t}".PHP_EOL;

        yield PHP_EOL;
    }
}