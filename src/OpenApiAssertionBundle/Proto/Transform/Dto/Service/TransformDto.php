<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Dto\Service;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;

final class TransformDto implements ServiceDtoInterface
{
    /**
     * @var ProtoClassDto[]
     */
    private array $protoClasses = [];

    public function getProtoClasses(): array
    {
        return $this->protoClasses;
    }

    public function setProtoClasses(array $protoClasses): self
    {
        $this->protoClasses = $protoClasses;

        return $this;
    }

    public static function getConstraints(): Constraint
    {
        return
            new Assert\Collection([
                'allowExtraFields' => true,
                'fields' => [
                    'protoClasses' => new Assert\All([
                        new Assert\NotNull(),
                        new Assert\Type(ProtoClassDto::class),
                    ]),
                ],
            ]);
    }
}