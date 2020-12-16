<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Http\Flow\Context;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\BasisBundle\Regular\Dto\Stateful\InvalidDto;
use Zinvapel\Basis\BasisBundle\Regular\Dto\ViolationDto;

final class InvalidContext implements ContextInterface
{
    private ConstraintViolationListInterface $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $this->constraintViolationList = $constraintViolationList;
    }

    public function apply(ServiceInterface $service): StatefulDtoInterface
    {
        return
            (new InvalidDto())
                ->setViolations($this->getViolations())
            ;
    }

    private function getViolations(): array
    {
        $violations = [];

        foreach ($this->constraintViolationList as $violation) {
            /* @var ConstraintViolationInterface $violation */

            $violations[] =
                (new ViolationDto())
                    ->setPath($violation->getPropertyPath())
                    ->setMessage($violation->getMessage())
                ;
        }

        return $violations;
    }
}