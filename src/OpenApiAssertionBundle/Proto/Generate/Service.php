<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate;

use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\BuildContext;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\BuildContext\Names;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\DirectorInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Builder\NameJoinStrategy\JoinStrategyInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Service\GenerateDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Stateful\GenerationDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Enumeration\Target;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\SchemaWrapper\Dto\SchemaWrapperDto;

final class Service implements ServiceInterface
{
    private const DELIMITER = '|';

    private array $directors;
    private JoinStrategyInterface $joinStrategy;

    /**
     * @param DirectorInterface[] $directors
     */
    public function __construct(array $directors, JoinStrategyInterface $joinStrategy)
    {
        $this->directors = $directors;
        $this->joinStrategy = $joinStrategy;
    }

    /**
     * @inheritdoc
     * @param GenerateDto $dto
     */
    public function perform(ServiceDtoInterface $dto): StatefulDtoInterface
    {
        $protoMaps = [];
        $wrapper = new SchemaWrapperDto($dto->getSchema());

        foreach ($this->directors as $director) {
            foreach ($wrapper->getEndpoints() as $name => $endpoint) {
                $buildContext =
                    new BuildContext(
                        $wrapper,
                        new Names(
                            explode(
                                self::DELIMITER,
                                preg_replace('/\W/u', self::DELIMITER, $name)
                            )
                        ),
                        $this->joinStrategy
                    );

                $protoClass = $director->build($endpoint, $buildContext);

                switch ($dto->getTarget()->getValue()) {
                    case Target::HTTP:
                        if (!isset($protoMaps[$director->getName()])) {
                            $protoMaps[$director->getName()] = [];
                        }

                        if ($protoClass !== null) {
                            $protoMaps[$director->getName()][$protoClass->getName()] = $protoClass;
                        }
                        break;
                    case Target::FULL:
                        $protoMaps = array_merge($protoMaps, $buildContext->getKnownObjects()->toArray());
                        break;
                    case Target::OBJECT:
                        if ($protoClass !== null && $protoClass->getName() === $dto->getObjectName()) {
                            $protoMaps[$protoClass->getName()] = $protoClass;
                        } else {
                            foreach ($buildContext->getKnownObjects()->toArray() as $objectName => $object) {
                                if ($objectName === $dto->getObjectName()) {
                                    $protoMaps[$objectName] = $object;
                                }
                            }
                        }
                        break;
                }
            }
        }

        return (new GenerationDto())->setProtoMaps($protoMaps);
    }
}