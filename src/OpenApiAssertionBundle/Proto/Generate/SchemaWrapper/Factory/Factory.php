<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Factory;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Dto\SchemaWrapperDto;

final class Factory implements FactoryInterface
{
    public function create(array $schema): SchemaWrapperDto
    {
        return new SchemaWrapperDto($schema);
    }
}