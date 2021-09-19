<?php

namespace App\Repository;

use App\Entity\Helo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
    public static  function NondeletedPostComments() :Criteria
    {
       return Criteria::create()
            ->andWhere(Criteria::expr()->eq('isDeleted',false))
            ->orderBy(['updatedAt'=>'DESC'])
        ;
    }
    public static  function CountDeletedCommnets() :Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('isDeleted',true))
            ;
    }
//    /**
//     * @param string|null $term
//     * @return Helo[]
//     */
//    public static function findwithsearch(?string $term)
//    {
//        if($term){
//            $query= (new HeloRepository)->createQueryBuilder('p')
//                ->andWhere('p.title LIKE :term ')
//                ->setParameter('term','%'.$term.'%')
//                ->getQuery()
//                ->getResult()
//
//            ;
//        }
//
//        return $query;
//
//    }
    /**
     * @param string|null $term
     * @return Helo[]
     */
    public function findwithsearch(?string $term)
    {
        if($term){
            $query=$this->createQueryBuilder('p')
                ->leftJoin('p.tags','a')
                ->addSelect('a')
                ->innerJoin('p.user','u')
                ->andWhere('p.content LIKE :term or p.createdAt LIKE :term or p.updatedAt LIKE :term or p.title LIKE :term or p.age LIKE :term or u.name Like :term or u.OGusername LikE :term  or a.name LIKE :term ')
                ->setParameter('term','%'.$term.'%')
                ->orderBy('p.updatedAt','DESC')
                ->getQuery()
                ->getResult()

            ;
        }

        else{
            $query=$this->findBy([],['updatedAt'=>'DESC']);
        }
        return $query;

    }
    /**
     * @param string|null $term
     * @return Helo[]
     */
    public function findwithtags(?string $q,?string $term)
    {
        if($q){
            if($term)
            {
                $query=$this->createQueryBuilder('p')
                    ->leftJoin('p.tags','a')
                    ->addSelect('a')
                    ->innerJoin('p.user','u')
                    ->where('a.slug=:q')
                    ->andWhere('p.content LIKE :term or p.createdAt LIKE :term or p.updatedAt LIKE :term or p.title LIKE :term or p.age LIKE :term or u.name Like :term or u.OGusername LikE :term  ')
                    ->setParameter('term','%'.$term.'%')
                    ->setParameter('q',$q)
                    ->orderBy('p.updatedAt','DESC')
                    ->getQuery()
                    ->getResult();
            }
            else{
                $query=$this->createQueryBuilder('p')
                    ->leftJoin('p.tags','a')
                    ->addSelect('a')
                    ->andWhere('  a.slug LIKE :q ')
                    ->setParameter('q',$q)
                    ->orderBy('p.updatedAt','DESC')
                    ->getQuery()
                    ->getResult()

                ;
            }

        }


        return $query;

    }
}
