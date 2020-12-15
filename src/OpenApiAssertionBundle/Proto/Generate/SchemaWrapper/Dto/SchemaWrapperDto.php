<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Dto;

final class SchemaWrapperDto
{
    private array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public function getEndpoints(): iterable
    {
        foreach ($this->schema['paths'] as $pathName => $path) {
            foreach ($path as $method => $info) {
                yield trim($pathName, '/').'|'.$method => $info;
            }
        }
    }

    public function getRef(string $ref): ?Ref
    {
        $context = $this->schema;
        $refPart = $ref;

        foreach (explode('/', $ref) as $refPart) {
            if ($refPart === '#') {
                continue;
            }

            if (!isset($context[$refPart])) {
                return null;
            }

            $context = $context[$refPart];
        }

        return new Ref((string) $refPart, $context);
    }
}