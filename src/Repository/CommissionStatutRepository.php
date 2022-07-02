<?php

namespace App\Repository;

use App\Entity\CommissionStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommissionStatut|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommissionStatut|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommissionStatut[]    findAll()
 * @method CommissionStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommissionStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommissionStatut::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CommissionStatut $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CommissionStatut $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CommissionStatut[] Returns an array of CommissionStatut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommissionStatut
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
