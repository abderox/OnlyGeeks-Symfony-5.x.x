<?php

namespace App\Repository;

use App\Entity\Iske;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Iske|null find($id, $lockMode = null, $lockVersion = null)
 * @method Iske|null findOneBy(array $criteria, array $orderBy = null)
 * @method Iske[]    findAll()
 * @method Iske[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IskeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Iske::class);
    }

    // /**
    //  * @return Iske[] Returns an array of Iske objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Iske
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
