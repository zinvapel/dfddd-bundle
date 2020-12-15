<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Factory;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Dto\SchemaWrapperDto;

interface FactoryInterface
{
    public function create(array $schema): SchemaWrapperDto;
}