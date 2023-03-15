<?php

namespace App\Repository;

use App\Entity\Circunscripcion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Circunscripcion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circunscripcion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circunscripcion[]    findAll()
 * @method Circunscripcion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CircunscripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circunscripcion::class);
    }


    public function findAllArray()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.circunscripcion', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findById($valueId)//por ID
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')    
            ->setParameter('id', $valueId)          
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllCircunscripciones()
    {
        $query = 'SELECT c.id, c.circunscripcion FROM App\Entity\Circunscripcion c WHERE c.estado = 0 ORDER BY c.circunscripcion';

        return $this->getEntityManager()
            ->createQuery($query)
            ->getResult();
    }


    // /**
    //  * @return Circunscripcion[] Returns an array of Circunscripcion objects
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
    public function findOneBySomeField($value): ?Circunscripcion
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
