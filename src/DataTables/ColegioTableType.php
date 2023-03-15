<?php

namespace App\DataTables;

use App\Entity\Colegio;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Omines\DataTablesBundle\Column\BoolColumn;

/**
 * TableType para datatables de grilla de Colegios
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 */
class ColegioTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de colegios
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->add('id', TextColumn::class, ['label' => 'Código', 'searchable' => false]);
        $dataTable->add('colegio', TextColumn::class, ['label' => 'Colegio', 'searchable' => true, 'leftExpr' => "toUpper(u.colegio)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        // COLUMNA ESTADO MANEJADA CON TEXTCOLUM
        // Para el campo Estado el FALSE or 0: Habilitado  y el TRUE or 1: Deshabilitado
        $dataTable->add('estado', TextColumn::class, ['label' => 'Estado', 'searchable' => false, 'field' => 'u.estado', 'leftExpr' => "toUpper(u.estado)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';},
            'render' => function ($value) {
                if($value == true){return 'Deshabilitado';}
                else{return 'Habilitado';}
            }
        ]);
        // COLUMNA VISIBLE MANEJADA CON BOOLCOLUM
        // Para el campo Visible el FALSE or 0: No Visible y el TRUE or 1: Visible
        $dataTable->add('visible', BoolColumn::class, ['label' => 'Visible', 'searchable' => false, 'field' => 'u.visible', 'className' => 'text-center', 'trueValue' => '<i class="fas fa-check"></i>', 'falseValue' => '<i class="fa fa-times"></i>', 'nullValue' => '', 'leftExpr' => "toUpper(u.visible)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'colegio/_actions_column.twig',
            ]);
        }
       
        // ORDENAMIENTO DE LA GRILLA
        $dataTable->addOrderBy('id', DataTable::SORT_ASCENDING);
        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Colegio::class,
            'query' => function (QueryBuilder $builder) {
  
                $builder
                    ->select('u')
                    ->from(Colegio::class, 'u');
            }
        ]);
    }

}
