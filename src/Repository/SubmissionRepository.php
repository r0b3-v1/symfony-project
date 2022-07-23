<?php

namespace App\Repository;

use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Submission $entity, bool $flush = true): void
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
    public function remove(Submission $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Submission[] Returns an array of Submission objects
    //  */
    public function quickSearch($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Submission[] Returns an array of Submission objects
     */
    public function search($params)
    {
        $title = $params['title'] ?? null;
        $authorId = $params['author'] ?? null;
        $description = $params['description'] ?? null;
        $categoryId = $params['categoryId'] ?? null;

        $query = $this->createQueryBuilder('s');
        if($title){
            $query = $query->andWhere('s.title LIKE :val1')
            ->setParameter('val1', '%' . $title . '%');
        }
        if($authorId){
            $query = $query->andWhere('s.author LIKE :val2')
            ->setParameter('val2', '%' . $authorId . '%');
        }
        if($description){
            $query = $query->andWhere('s.description LIKE :val3')
            ->setParameter('val3', '%' . $description . '%');
        }
        if($categoryId){
            $query = $query->andWhere('s.category = :val4')
            ->setParameter('val4', $categoryId);
        }

        return $query->getQuery()->getResult();
        
    }

    /*
    public function findOneBySomeField($value): ?Submission
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
