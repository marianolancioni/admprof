<?php

namespace App\Repository;

use App\Entity\ColegioCirc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ColegioCirc|null find($id, $lockMode = null, $lockVersion = null)
 * @method ColegioCirc|null findOneBy(array $criteria, array $orderBy = null)
 * @method ColegioCirc[]    findAll()
 * @method ColegioCirc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColegioCircRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ColegioCirc::class);
    }


    public function findByColeCirc($valueCole, $valueCirc)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.colegio = :val')
            ->andWhere('c.circunscripcion = :val1')
            ->setParameter('val', $valueCole)
            ->setParameter('val1', $valueCirc)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return ColegioCirc[] Returns an array of ColegioCirc objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ColegioCirc
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
