<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Service;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\Validatable;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Enumeration\Target;

final class GenerateDto implements ServiceDtoInterface, Validatable
{
    private array $schema;
    private Target $target;
    private ?string $objectName = null;

    public function getSchema(): array
    {
        return $this->schema;
    }

    public function setSchema(array $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function getTarget(): Target
    {
        return $this->target;
    }

    public function setTarget(Target $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getObjectName(): ?string
    {
        return $this->objectName;
    }

    public function setObjectName(?string $objectName): self
    {
        $this->objectName = $objectName;

        return $this;
    }

    public static function getConstraints(): array
    {
        return [
            new Assert\Collection([
                'allowExtraFields' => true,
                'fields' => [
                    'schema' => [
                        new Assert\NotNull(),
                        new Assert\Type('array'),
                    ],
                    'target' => [
                        new Assert\NotNull(),
                        new Assert\Choice(['choices' => Target::getValuesList()]),
                    ],
                    'objectName' => new Assert\Optional([
                        new Assert\Type('string')
                    ]),
                ],
            ])
        ];
    }
}