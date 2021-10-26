<?php

namespace App\Repository;

use App\Entity\ProxyList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProxyList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProxyList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProxyList[]    findAll()
 * @method ProxyList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProxyListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProxyList::class);
    }

    // /**
    //  * @return ProxyList[] Returns an array of ProxyList objects
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
    public function findOneBySomeField($value): ?ProxyList
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
