<?php

namespace App\Repository;

use App\Entity\PostDislikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostDislikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostDislikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostDislikes[]    findAll()
 * @method PostDislikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostDislikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostDislikes::class);
    }

    // /**
    //  * @return PostDislikes[] Returns an array of PostDislikes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostDislikes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
