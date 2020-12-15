<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\PropertyBuilder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

final class Request extends Base
{
    public function build(ProtoClassDto $proto, BuildContext $context, array $data): void
    {
        $properties = [];

        foreach ($data as $partName => $part) {
            switch ($partName) {
                case 'requestBody':
                    $context->setSerializationGroups(['body']);

                    foreach ($part['content'] ?? [] as $format => $schema) {
                        $virtualProperty = $this->createProperty($schema['schema'], $context, true);
                        $properties = array_merge($properties, $virtualProperty->getObjectType()->getProperties());
                    }
                    break;
                case 'parameters':
                    foreach ($part as $parameter) {
                        $context->getNames()->pushName($parameter['name']);
                        $context->setSerializationGroups([$parameter['in']]);

                        $properties[] =
                            $this->createProperty(
                                $parameter['schema'],
                                $context,
                                $parameter['required']
                            )
                        ;

                        $context->getNames()->popName();
                    }
            }
        }

        $proto->setProperties($properties);
    }
}