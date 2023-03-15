<?php

namespace App\Repository;

use App\Entity\UsuarioLogAccion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsuarioLogAccion|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioLogAccion|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioLogAccion[]    findAll()
 * @method UsuarioLogAccion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioLogAccionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioLogAccion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UsuarioLogAccion $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(UsuarioLogAccion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return UsuarioLogAccion[] Returns an array of UsuarioLogAccion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsuarioLog
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
