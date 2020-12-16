<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString;

use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Dto\Stateful\TransformationDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Transformer\TransformerInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Dto\Service\TransformDto;

final class TransformProtoToClassService implements ServiceInterface
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
        $classes = [];

        foreach ($dto->getProtoClasses() as $name => $protoClass) {
            $classes[$name] = '';
            foreach ($this->transformer->transform($protoClass) as $string) {
                $classes[$name] .= $string;
            }
        }

        return (new TransformationDto())->setClasses($classes);
    }
}