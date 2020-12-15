<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert;

use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Dto\Stateful\TransformationDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Transformer\TransformerInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Dto\Service\TransformDto;

final class Service implements ServiceInterface
{
    private TransformerInterface $transformer;

    public function __construct(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @inheritdoc
     * @param TransformDto $dto
     */
    public function perform(ServiceDtoInterface $dto): StatefulDtoInterface
    {
        $assertions = [];

        foreach ($dto->getProtoClasses() as $name => $protoClass) {
            $assertions[$name] = '';

            foreach ($protoClass->getAssertions() as $assert) {
                foreach ($this->transformer->transform($assert, 0) as $string) {
                    $assertions[$name] .= $string;
                }
            }
        }

        return (new TransformationDto())->setAssertion($assertions);
    }
}