<?php

namespace App\Repository;

use App\Entity\Helo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Helo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Helo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Helo[]    findAll()
 * @method Helo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Helo::class);
    }

    // /**
    //  * @return Helo[] Returns an array of Helo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Helo
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
