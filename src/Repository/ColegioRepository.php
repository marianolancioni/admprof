<?php

namespace App\Repository;

use App\Entity\Colegio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Colegio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colegio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colegio[]    findAll()
 * @method Colegio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColegioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colegio::class);
    }


    public function findAllArray()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.colegio', 'ASC')
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

    public function findAllColegios()
    {
        $query = 'SELECT c.id, c.colegio FROM App\Entity\Colegio c WHERE c.estado = 0 ORDER BY c.colegio';

        return $this->getEntityManager()
            ->createQuery($query)
            ->getResult();
    }    


    // /**
    //  * @return Colegio[] Returns an array of Colegio objects
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
    public function findOneBySomeField($value): ?Colegio
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
