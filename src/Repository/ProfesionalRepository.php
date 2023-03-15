<?php

namespace App\Repository;

use App\Entity\Profesional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Profesional|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profesional|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profesional[]    findAll()
 * @method Profesional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profesional::class);
    }


    public function findByMatricu($value, $valueCole, $valueCirc) //MATRICULA COLEGIO Y CIRCUNSCRIPCIÓN
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :val2')
            ->andWhere('p.colegio = :val1')
            ->andWhere('p.matricula = :val')
            ->setParameter('val2', $valueCirc)
            ->setParameter('val1', $valueCole)
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByColeCir($valueCole, $valueCirc, $fechaDesde, $claves) //MATRICULA COLEGIO Y CIRCUNSCRIPCIÓN
    {

        if ($valueCirc <> 0 && $valueCole <> 99) {
            if (empty($fechaDesde)) {
                return $this->createQueryBuilder('p')
                    ->andWhere('p.circunscripcion = :val2')
                    ->andWhere('p.colegio = :val1')
                    ->setParameter('val2', $valueCirc)
                    ->setParameter('val1', $valueCole)
                    ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                    ->getQuery()
                    ->getResult();
            } else {
                if ($claves) {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.circunscripcion = :val2')
                        ->andWhere('p.colegio = :val1')
                        ->andWhere('p.fechaClave >= :val3')
                        ->setParameter('val2', $valueCirc)
                        ->setParameter('val1', $valueCole)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                } else {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.circunscripcion = :val2')
                        ->andWhere('p.colegio = :val1')
                        ->andWhere('p.fechaActualizacion >= :val3')
                        ->setParameter('val2', $valueCirc)
                        ->setParameter('val1', $valueCole)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                }
            }
        }

        if ($valueCirc == 0 && $valueCole <> 99) { //todas las circunscripciones
            if (empty($fechaDesde)) {
                return $this->createQueryBuilder('p')
                    ->andWhere('p.colegio = :val1')
                    ->setParameter('val1', $valueCole)
                    ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                    ->getQuery()
                    ->getResult();
            } else {
                if ($claves) {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.colegio = :val1')
                        ->andWhere('p.fechaClave >= :val3')
                        ->setParameter('val1', $valueCole)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                } else {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.colegio = :val1')
                        ->andWhere('p.fechaActualizacion >= :val3')
                        ->setParameter('val1', $valueCole)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                }
            }
        }

        if ($valueCirc <> 0 && $valueCole == 99) { //todos los colegios
            if (empty($fechaDesde)) {
                return $this->createQueryBuilder('p')
                    ->andWhere('p.circunscripcion = :val2')
                    ->setParameter('val2', $valueCirc)
                    ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                    ->getQuery()
                    ->getResult();
            } else {
                if ($claves) {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.circunscripcion = :val2')
                        ->andWhere('p.fechaClave >= :val3')
                        ->setParameter('val2', $valueCirc)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                } else {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.circunscripcion = :val2')
                        ->andWhere('p.fechaActualizacion >= :val3')
                        ->setParameter('val2', $valueCirc)
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                }
            }
        }

        if ($valueCirc == 0 && $valueCole == 99) { //todos los colegios y circunscripciones
            if (empty($fechaDesde)) {
                return $this->createQueryBuilder('p')
                    ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                    ->getQuery()
                    ->getResult();
            } else {
                if ($claves) {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.fechaClave >= :val3')
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                } else {
                    return $this->createQueryBuilder('p')
                        ->andWhere('p.fechaActualizacion >= :val3')
                        ->setParameter('val3', $fechaDesde)
                        ->orderBy('p.id', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
                        ->getQuery()
                        ->getResult();
                }
            }
        }
    }

    //PROFESIONALES DE UN COLEGIO Y CIRCUNSCRIPCION
    public function findByColegioCircunscripcion($valueColegio, $valueCircunscripcion) //MATRICULA COLEGIO Y CIRCUNSCRIPCIÓN
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.colegio = :col')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)
            ->orderBy('p.apellido', 'ASC')  //ver mejorar orden,no trabajamos con indices todavía
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //Circunscripcion 0 => TODAS
    //Colegio 99 => TODOS
    //CONSULTA PARA Listado de Matrículas por fecha de creación
    public function findByColegioCircunscripcionRangoAlta($valueIdColegio, $valueIdCircunscripcion, $valueFechaDesde, $valueFechaHasta) //MATRICULA COLEGIO Y CIRCUNSCRIPCIÓN
    {
        // FILTRO LOS PROFESIONALES POR UN COLEGIO, CIRCUNSCRIPCION Y RANGO FECHA
        if ($valueIdColegio != 99 and $valueIdCircunscripcion != 0) {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fechaAlta BETWEEN :fechaDesde AND :fechaHasta')
                ->andWhere('p.circunscripcion = :idCircunscripcion')
                ->andWhere('p.colegio = :idColegio')
                ->setParameter('idCircunscripcion', $valueIdCircunscripcion)
                ->setParameter('idColegio', $valueIdColegio)
                ->setParameter('fechaDesde', $valueFechaDesde)
                ->setParameter('fechaHasta', $valueFechaHasta)
                ->orderBy('p.matricula', 'ASC')
                ->getQuery()
                ->getResult();
        }
        // FILTRO LOS PROFESIONALES SOLO CIRCUNSCRIPCION Y RANGO FECHA
        elseif ($valueIdColegio = 99 and $valueIdCircunscripcion != 0) {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fechaAlta BETWEEN :fechaDesde AND :fechaHasta')
                ->andWhere('p.circunscripcion = :idCircunscripcion')
                ->setParameter('idCircunscripcion', $valueIdCircunscripcion)
                ->setParameter('fechaDesde', $valueFechaDesde)
                ->setParameter('fechaHasta', $valueFechaHasta)
                ->orderBy('p.matricula', 'ASC')
                ->getQuery()
                ->getResult();
        }
        // FILTRO LOS PROFESIONALES POR COLEGIO Y RANGO FECHA
        elseif ($valueIdColegio != 99 and $valueIdCircunscripcion = 0) {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fechaAlta BETWEEN :fechaDesde AND :fechaHasta')
                ->andWhere('p.colegio = :idColegio')
                ->setParameter('idColegio', $valueIdColegio)
                ->setParameter('fechaDesde', $valueFechaDesde)
                ->setParameter('fechaHasta', $valueFechaHasta)
                ->orderBy('p.matricula', 'ASC')
                ->getQuery()
                ->getResult();
        }
        // FILTRO LOS PROFESIONALES SOLO POR RANGO FECHA
        else {
            return $this->createQueryBuilder('p')
                ->andWhere('p.fechaAlta BETWEEN :fechaDesde AND :fechaHasta')
                ->setParameter('fechaDesde', $valueFechaDesde)
                ->setParameter('fechaHasta', $valueFechaHasta)
                ->orderBy('p.matricula', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }

    public function findByColegCircRangoFechaClave($valueColegio, $valueCircunscripcion, $valueFechaClaveDesde, $valueFechaClavehasta) //COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE ASIGNACION DE CLAVE
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaClave <= :fecH')
            ->andWhere('p.fechaClave >= :fecD')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)
            ->setParameter('fecH', $valueFechaClavehasta)
            ->setParameter('fecD', $valueFechaClaveDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRangoFechaClave($valueFechaClaveDesde, $valueFechaClavehasta) //RANGO DE FECHA DE ASIGNACION DE CLAVE
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fechaClave <= :fecH')
            ->andWhere('p.fechaClave >= :fecD')
            ->setParameter('fecH', $valueFechaClavehasta)
            ->setParameter('fecD', $valueFechaClaveDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByColegRangoFechaClave($valueColegio, $valueFechaClaveDesde, $valueFechaClavehasta) //COLEGIO Y RANGO DE FECHA DE ASIGNACION DE CLAVE
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaClave <= :fecH')
            ->andWhere('p.fechaClave >= :fecD')
            ->setParameter('fecH', $valueFechaClavehasta)
            ->setParameter('fecD', $valueFechaClaveDesde)
            ->setParameter('col', $valueColegio)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCircRangoFechaClave($valueCircunscripcion, $valueFechaClaveDesde, $valueFechaClavehasta) //CIRCUNSCRIPCION Y RANGO DE FECHA DE ASIGNACION DE CLAVE
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.fechaClave <= :fecH')
            ->andWhere('p.fechaClave >= :fecD')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('fecH', $valueFechaClavehasta)
            ->setParameter('fecD', $valueFechaClaveDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByMatriculaColegioCircunscripcion($valueIdColegio, $valueIdCircunscripcion, $valueMatricula)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('upper(p.matricula) LIKE :matricula')
            ->andWhere('p.circunscripcion = :idCircunscripcion')
            ->andWhere('p.colegio = :idColegio')
            ->setParameter('idCircunscripcion', $valueIdCircunscripcion)
            ->setParameter('idColegio', $valueIdColegio)
            ->setParameter('matricula', strtoupper($valueMatricula))
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByColegCircRangoFechaModificacion($valueColegio, $valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta) //COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaActualizacion <= :fecH')
            ->andWhere('p.fechaActualizacion >= :fecD')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)
            ->setParameter('fecH', $valueFechaModifhasta)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRangoFechaModificacion($valueFechaModifDesde, $valueFechaModifhasta) //RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fechaActualizacion <= :fecH')
            ->andWhere('p.fechaActualizacion >= :fecD')
            ->setParameter('fecH', $valueFechaModifhasta)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByColegRangoFechaModificacion($valueColegio, $valueFechaModifDesde, $valueFechaModifhasta) //COLEGIO Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaActualizacion <= :fecH')
            ->andWhere('p.fechaActualizacion >= :fecD')
            ->setParameter('fecH', $valueFechaModifhasta)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->setParameter('col', $valueColegio)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCircRangoFechaModificacion($valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta) //CIRCUNSCRIPCION Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.fechaActualizacion <= :fecH')
            ->andWhere('p.fechaActualizacion >= :fecD')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('fecH', $valueFechaModifhasta)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->orderBy('p.apellido', 'ASC')
            ->addOrderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }



    //invocado desde reporte de Modificaciones y Asignaciones por Colegios
    public function findByCantiMod($valueColegio, $valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta) //COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaActualizacion >= :fecD')
            ->andWhere('p.fechaActualizacion <= :fecH')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->setParameter('fecH', $valueFechaModifhasta)
            ->getQuery()
            ->getSingleScalarResult();
    }


    //invocado desde reporte de Modificaciones y Asignaciones por Colegios
    public function findByCantiAsigna($valueColegio, $valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta) //COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.circunscripcion = :circ')
            ->andWhere('p.colegio = :col')
            ->andWhere('p.fechaClave >= :fecD')
            ->andWhere('p.fechaClave <= :fecH')
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)
            ->setParameter('fecD', $valueFechaModifDesde)
            ->setParameter('fecH', $valueFechaModifhasta)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByMatricuCount($value, $valueCole, $valueCirc) //MATRICULA COLEGIO Y CIRCUNSCRIPCIÓN
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.circunscripcion = :val2')
            ->andWhere('p.colegio = :val1')
            ->andWhere('p.matricula = :val')
            ->setParameter('val2', $valueCirc)
            ->setParameter('val1', $valueCole)
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retorna una lista de los profesionales a los cuales se le actualizó la fechaActualizacion ó fechaClave desde ayer hasta el momento
     * @param int ID de la Circunscripcion para filtrar
     * @param int ID del Colegio para filtrar
     * @param string Fecha desde a buscar novedades
     * @param string Fecha hasta a buscar novedades
     * @param bool Si se busca modificaciones en claves (Default = false)
     * @return mixed Array con profesionales
     */
    public function findNovedadesParaExportacion(int $idCircuncionscripcion, int $idColegio, string $fechaDesde, string $fechaHasta, bool $isModifClave = false): mixed
    {

        $qb = $this->createQueryBuilder('p');
        return $qb
            ->distinct('p.id')
            ->where('p.circunscripcion = :id_circunscripcion')
            ->andWhere('p.colegio = :id_colegio')
            ->andWhere($isModifClave ? $qb->expr()->between('p.fechaClave', $fechaDesde, $fechaHasta) : $qb->expr()->between('p.fechaActualizacion', $fechaDesde, $fechaHasta))
            ->setParameter('id_circunscripcion', $idCircuncionscripcion)
            ->setParameter('id_colegio', $idColegio)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Profesional[] Returns an array of Profesional objects
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
    public function findOneBySomeField($value): ?Profesional
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


  //invocado desde reporte de Modificaciones y Asignaciones por Colegios
    public function findByColegCircFechaModificacion($valueColegio, $valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta)//COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')    
            ->andWhere('p.colegio = :col')  
            ->andWhere('p.fechaActualizacion >= :fecD')  
            ->andWhere('p.fechaActualizacion <= :fecH')        
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)  
            ->setParameter('fecD', $valueFechaModifDesde)   
            ->setParameter('fecH', $valueFechaModifhasta)
            ->orderBy('p.apellido', 'ASC')  
            ->getQuery()
            ->getResult()
        ;
    }

    //invocado desde reporte de Modificaciones y Asignaciones por Colegios
    public function findByColegCircFechaAsignacion($valueColegio, $valueCircunscripcion, $valueFechaModifDesde, $valueFechaModifhasta)//COLEGIO, CIRCUNSCRIPCIÓN Y RANGO DE FECHA DE MODIFICACION
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.circunscripcion = :circ')    
            ->andWhere('p.colegio = :col')  
            ->andWhere('p.fechaClave >= :fecD')   
            ->andWhere('p.fechaClave <= :fecH')      
            ->setParameter('circ', $valueCircunscripcion)
            ->setParameter('col', $valueColegio)  
            ->setParameter('fecD', $valueFechaModifDesde) 
            ->setParameter('fecH', $valueFechaModifhasta)  
            ->orderBy('p.apellido', 'ASC')  
            ->getQuery()
            ->getResult()
        ;
    }

    */
}
