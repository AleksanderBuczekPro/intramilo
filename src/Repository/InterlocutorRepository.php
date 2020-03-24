<?php

namespace App\Repository;

use App\Entity\Interlocutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Interlocutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interlocutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interlocutor[]    findAll()
 * @method Interlocutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterlocutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interlocutor::class);
    }

    // /**
    //  * @return Interlocutor[] Returns an array of Interlocutor objects
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
    public function findOneBySomeField($value): ?Interlocutor
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
