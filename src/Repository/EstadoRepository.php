<?php

namespace App\Repository;

use App\Entity\Estado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Estado|null find($id, $lockMode = null, $lockVersion = null)
 * @method Estado|null findOneBy(array $criteria, array $orderBy = null)
 * @method Estado[]    findAll()
 * @method Estado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estado::class);
    }

    
    public function findOneByEstado($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :val')    
            ->setParameter('val', $value)       
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Estado[] Returns an array of Estado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Estado
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
