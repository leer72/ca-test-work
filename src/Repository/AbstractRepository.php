<?php

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;

/**
 * @template-extends ServiceEntityRepository<EntityInterface>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @throws Exception
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function add(EntityInterface $entity): EntityInterface
    {
        $this->_em->getConnection()->setNestTransactionsWithSavepoints(true);

        $this->_em->wrapInTransaction(fn () => $this->_em->persist($entity));

        return $entity;
    }

    /**
     * @throws Exception
     */
    public function remove(EntityInterface $entity): void
    {
        $this->_em->getConnection()->setNestTransactionsWithSavepoints(true);

        $this->_em->wrapInTransaction(fn () => $this->_em->remove($entity));
    }

    public function persist(EntityInterface $entity): void
    {
        $this->_em->persist($entity);
    }

    /**
     * @throws Exception
     * @param EntityInterface $newEntity
     *
     * @param EntityInterface $oldEntity
     * @return EntityInterface
     */
    public function changeEntity(
        EntityInterface $oldEntity,
        EntityInterface $newEntity,
    ): EntityInterface {
        $this->_em->getConnection()->setNestTransactionsWithSavepoints(true);

        $this->_em->wrapInTransaction(
            function () use ($oldEntity, $newEntity): void {
                $this->_em->remove($oldEntity);
                $this->_em->flush();
                $this->_em->persist($newEntity);
            },
        );

        return $newEntity;
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
