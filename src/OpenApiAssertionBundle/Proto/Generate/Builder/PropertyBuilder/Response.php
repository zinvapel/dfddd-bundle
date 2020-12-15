<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

final class Response extends Base
{
    public function build(ProtoClassDto $proto, BuildContext $context, array $data): void
    {
        $context->setSerializationGroups(['body']);
        $properties = [];

        foreach ($data['responses'] ?? [] as $status => $part) {
            foreach ($part['content'] ?? [] as $format => $schema) {
                $virtualProperty = $this->createProperty($schema['schema'], $context, true);
                $properties = array_merge($properties, $virtualProperty->getObjectType()->getProperties());
            }
        }

        $proto->setProperties($properties);
    }
}