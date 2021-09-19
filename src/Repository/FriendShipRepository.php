<?php

namespace App\Repository;

use App\Entity\FriendShip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FriendShip|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendShip|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendShip[]    findAll()
 * @method FriendShip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendShip::class);
    }

    // /**
    //  * @return FriendShip[] Returns an array of FriendShip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FriendShip
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
