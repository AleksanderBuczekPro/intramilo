<?php

namespace App\Repository;

use App\Entity\Antenne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Antenne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Antenne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Antenne[]    findAll()
 * @method Antenne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AntenneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Antenne::class);
    }

    // /**
    //  * @return Antenne[] Returns an array of Antenne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Antenne
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
