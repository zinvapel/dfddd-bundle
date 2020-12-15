<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\AssertionBuilder;

use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

interface AssertionBuilderInterface
{
    public function build(ProtoClassDto $proto, BuildContext $context, array $data): void;
}