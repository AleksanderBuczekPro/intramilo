<?php

namespace App\Repository;

use App\Entity\SheetTools;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SheetTools|null find($id, $lockMode = null, $lockVersion = null)
 * @method SheetTools|null findOneBy(array $criteria, array $orderBy = null)
 * @method SheetTools[]    findAll()
 * @method SheetTools[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SheetToolsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SheetTools::class);
    }

    // /**
    //  * @return SheetTools[] Returns an array of SheetTools objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SheetTools
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
