<?php

namespace App\DataTables;

use App\Entity\Estado;
use App\Entity\Profesional;
use App\Repository\EstadoRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de Profesionales
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 */
class ProfesionalTableType extends AbstractController implements DataTableTypeInterface
{

    private $_estadoRepository;
    
    public function __construct(EstadoRepository $estadoRepository)
    {
        $this->_estadoRepository =  $estadoRepository;
    }
    

    /**
     * Configuro las columnas y sus funcionamiento de la grilla de profesionales
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
       
        $dataTable->add('matricula', TextColumn::class, ['label' => 'Matrícula', 'searchable' => true, 'leftExpr' => "toUpper(u.matricula)", 'rightExpr' => function ($value) {
            return '%' . mb_strtoupper($value) . '%';
        }]);
       $dataTable->add('apellido', TextColumn::class, ['label' => 'Apellido', 'searchable' => true, 'leftExpr' => "toUpper(u.apellido)", 'rightExpr' => function ($value) {
            return '%' . mb_strtoupper($value) . '%';
        }]);
        $dataTable->add('nombre', TextColumn::class, ['label' => 'Nombre', 'searchable' => false, 'leftExpr' => "toUpper(u.nombre)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('domicilio', TextColumn::class, ['label' => 'Domicilio', 'searchable' => false, 'leftExpr' => "toUpper(u.domicilio)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('localidad', TextColumn::class, ['label' => 'Localidad', 'searchable' => false, 'field' => 'u.localidad', 'leftExpr' => "toUpper(u.localidad)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
       
        if (!$this->getUser()->getColegio()) {
            $dataTable->add('colegio', TextColumn::class, ['label' => 'Colegio', 'searchable' => false, 'field' => 'u.colegio', 'leftExpr' => "toUpper(u.colegio)", 'rightExpr' => function ($value) {
                return '%' . strtoupper($value) . '%';
            }]);
        }
        if (!$this->getUser()->getCircunscripcion()) {
            $dataTable->add('circunscripcion', TextColumn::class, ['label' => 'Circ.', 'searchable' => false, 'field' => 'u.circunscripcion', 'leftExpr' => "toUpper(u.circunscripcion)", 'rightExpr' => function ($value) {
                return '%' . strtoupper($value) . '%';
            }]);
        }
        $dataTable->add('estadoProfesional', TextColumn::class, ['label' => 'Estado', 'searchable' => false, 'field' => 'u.estadoProfesional', 'leftExpr' => "toUpper(u.estadoProfesional)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);

        if ($this->isGranted('ROLE_ADMIN')) {
            $dataTable->add('fechaBaja', DateTimeColumn::class, ['label' => 'Fecha Baja','format' => 'd/m/y', 'searchable' => false, 'leftExpr' => "toUpper(u.fechaBaja)", 'rightExpr' => function ($value) {
                return '%' . strtoupper($value) . '%';
            }]);
        }
        $dataTable->add('estado', TextColumn::class, ['label' => 'EstadoBaja','visible' => false, 'searchable' => false, 'leftExpr' => "toUpper(u.estado)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
       
       

       
        if ($this->isGranted('ROLE_CONSULTOR') || $this->isGranted('ROLE_ADMIN')) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'profesional/_actions_column.twig',
            ]);
        }
        

        // Orden de la grilla
        $dataTable->addOrderBy('apellido', DataTable::SORT_ASCENDING);
        $dataTable->addOrderBy('nombre', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            
            'entity' => Profesional::class,
            'query' => function (QueryBuilder $builder) use ($options)  {
                $colegioUsuario = $this->getUser()->getColegio();
                $circunscripcionUsuario = $this->getUser()->getCircunscripcion(); 
                                
                $builder
                    ->select('u')
                    ->from(Profesional::class, 'u');

                // Si el usuario está asociado a una Circunscripción, filtro por la misma
                if ($circunscripcionUsuario) {
                    $builder
                        ->andwhere('u.circunscripcion = :circunscripcion')
                        ->setParameter('circunscripcion', $circunscripcionUsuario);
                }

                // Si el usuario está asociado a un Colegio, filtro por el mismo
                if ($colegioUsuario) {
                    $builder
                        ->andwhere('u.colegio = :colegio')
                        ->setParameter('colegio', $colegioUsuario);
                }
                
                // Evalúo si filtrar por estadoProfesional
                if (count($options)>0 && $options[0]>0) {
                    $opcionEstado = $options[0];
                    $estadoProfesional = $this->_estadoRepository->findOneByEstado($opcionEstado);
                    if ($opcionEstado) {
                        $builder
                            ->andwhere('u.estadoProfesional = :estadoProfesional')
                            ->setParameter('estadoProfesional', $estadoProfesional);
                    }                
                }

                // Evalúo si filtrar por estadoBaja
                
                //si no es admin que estado sea alta
                if ($this->isGranted('ROLE_ADMIN')) {
                    if (count($options)>1 && $options[1] < 2 ) {
                        $estadoBaja = $options[1];  
                        $builder
                            ->andwhere('u.estado = :estadoBaja')
                            ->setParameter('estadoBaja', $estadoBaja);                       
                    }                      
                }else{// si no es admin no ve da baja
                    $estadoBaja =0; //0:activo  1:baja                    
                    $builder
                        ->andwhere('u.estado = :estadoBaja')
                        ->setParameter('estadoBaja', $estadoBaja);
                }
                //
            

                // Evalúo si filtrar por Circunscripción
                if (!$circunscripcionUsuario && count($options) > 2 && $options[2] ) {
                    $circunscripcionId = $options[2];
                    $builder
                        ->andwhere('u.circunscripcion = :circunscripcion')
                        ->setParameter('circunscripcion', $circunscripcionId);
                } 
                
                // Evalúo si filtrar por Colegio (99 Representa Todos los Colegios)
                if (!$colegioUsuario && count($options) > 3 && $options[3] != 99 ) {
                    $colegioId = $options[3];
                    $builder
                        ->andwhere('u.colegio = :colegio')
                        ->setParameter('colegio', $colegioId);
                }     
                  
            }
        ]);
    }
}
