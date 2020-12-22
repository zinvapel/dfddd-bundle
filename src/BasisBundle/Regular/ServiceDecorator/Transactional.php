<?php
declare(strict_types=1);

namespace Zinvapel\Basis\BasisBundle\Regular\ServiceDecorator;

use Doctrine\ORM\EntityManagerInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInterface;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Throwable;

final class Transactional implements ServiceInterface
{
    private EntityManagerInterface $entityManager;
    private ServiceInterface $inner;
    private bool $rollbackOnlyOnException;
    
    public function __construct(
        EntityManagerInterface $entityManager, 
        ServiceInterface $inner,
        bool $rollbackOnlyOnException = true
    ) {
        $this->entityManager = $entityManager;
        $this->inner = $inner;
        $this->rollbackOnlyOnException = $rollbackOnlyOnException;
    }

    public function perform(ServiceDtoInterface $dto): StatefulDtoInterface
    {
        try {
            $this->entityManager->beginTransaction();
            
            $innerResult = $this->inner->perform($dto);
            
            if (!$innerResult->getState()->isSuccess() && !$this->rollbackOnlyOnException) {
                $this->entityManager->close();
                $this->entityManager->rollback();
            } else {
                $this->entityManager->flush();
                $this->entityManager->commit();
            }
            
            return $innerResult;
        } catch (Throwable $t) {
            $this->entityManager->close();
            $this->entityManager->rollback();
            
            throw $t;
        }
    }
}