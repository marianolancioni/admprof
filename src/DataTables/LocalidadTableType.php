<?php

namespace App\DataTables;

use App\Entity\Localidad;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de Localidades
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 */
class LocalidadTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de localidades
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->add('id', TextColumn::class, ['label' => 'Código', 'searchable' => false]);
        $dataTable->add('localidad', TextColumn::class, ['label' => 'Localidad', 'searchable' => true, 'leftExpr' => "toUpper(u.localidad)", 'rightExpr' => function ($value) {
            return '%' .mb_strtoupper($value) . '%';
        }]);
        $dataTable->add('codigoPostal', TextColumn::class, ['label' => 'Código Postal', 'searchable' => false, 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('subCodigoPostal', TextColumn::class, ['label' => 'Sub Código Postal', 'searchable' => false, 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('provincia', TextColumn::class, ['label' => 'Provincia', 'searchable' => false, 'field' => 'u.provincia', 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        // COLUMNA ESTADO MANEJADA CON TEXTCOLUM
        /*Para el campo Estado el FALSE or 0: Habilitado  y el TRUE or 1: Deshabilitado*/ 
        $dataTable->add('estado', TextColumn::class, ['label' => 'Estado', 'searchable' => false, 'field' => 'u.estado', 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';},
            'render' => function ($value) {
                if($value == true){return 'Deshabilitado';}
                else{return 'Habilitado';}
            }
        ]);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'localidad/_actions_column.twig',
            ]);
        }
        
        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('localidad', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Localidad::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('u')
                    ->from(Localidad::class, 'u');
            }
        ]);
    }
}
