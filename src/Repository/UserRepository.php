<?php

namespace App\Repository;

use App\Entity\Statut;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
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
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function search($params)
    {
        $name = $params['name'] ?? null;
        $dispo = $params['dispo'] ?? null;
        $option = $params['option'] ?? null;

        //on filtre uniquement les utilisateurs qui sont des artistes
        $query = $this->createQueryBuilder('u')
            ->join(Statut::class, 's', Join::WITH , 'u.statut=s.id')
            ->andWhere('s.name = :val')
            ->setParameter('val','artiste')
            

            ;
        

        if($name){
            // $query = $query->andWhere('u.username LIKE :val')
            // ->setParameter('val', '%' . $name . '%');
        }
        
        if($dispo){
            $query = $query->andWhere('u.disponible = :val2')
            ->setParameter('val2', 1);
        }
        else{
            $query = $query->andWhere('u.disponible = :val2')
            ->setParameter('val2', 0);
        }
        
        if($option){

            switch ($option) {
                case 'date_asc':
                    $query = $query->orderBy('u.registration_date', 'ASC');
                    break;
                case 'date_desc':
                    $query = $query->orderBy('u.registration_date', 'DESC');
                    break;
                
                default:
                    break;
            }

        }



        
        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
