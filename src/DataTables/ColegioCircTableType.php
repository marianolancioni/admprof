<?php

namespace App\DataTables;

use App\Entity\ColegioCirc;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de ColegioCirc
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class ColegioCircTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de ColegioCirc
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->add('id', TextColumn::class, ['label' => 'Código', 'searchable' => false]);
        $dataTable->add('colegio', TextColumn::class, ['label' => 'Colegio', 'className' => 'text-center', 'searchable' => true, 'field' => 'cc.colegio', 'leftExpr' => "toUpper(col.colegio)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('circunscripcion', TextColumn::class, ['label' => 'Circunscripción', 'className' => 'text-center', 'searchable' => true, 'field' => 'cc.circunscripcion', 'leftExpr' => "toUpper(cir.circunscripcion)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('caracteresPermitidos', TextColumn::class, ['label' => 'Caracteres Permitidos', 'searchable' => false, 'field' => 'cc.caracteresPermitidos', 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('profesionales', NumberColumn::class, ['field' => 'cc.cantProf', 'label' => 'Cant. Profesionales', 'className' => 'text-end', 'render' => function ($value) {
            return number_format($value, 0, ',', '.');
        }]);
        // PROPERTY VISIBLE MANDEJADA COMO BOOL DESDE LA BD
        // COLUMNA ESTADO MANEJADA CON TEXTCOLUM
        // Para el campo Estado el FALSE or 0: Habilitado  y el TRUE or 1: Deshabilitado
        $dataTable->add('estado', TextColumn::class, ['label' => 'Estado', 'className' => 'text-center', 'searchable' => false, 'field' => 'cc.estado', 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';},
            'render' => function ($value) {
                if($value == true){return 'Deshabilitado';}
                else{return 'Habilitado';}
            }
        ]);
        // PROPERTY VISIBLE MANDEJADA COMO BOOL DESDE LA BD
        // COLUMNA VISIBLE MANEJADA CON BOOLCOLUM
        // Para el campo Visible el FALSE or 0: No Visible y el TRUE or 1: Visible
        $dataTable->add('visible', BoolColumn::class, ['label' => 'Visible', 'searchable' => false, 'field' => 'cc.visible', 'className' => 'text-center', 'trueValue' => '<i class="fas fa-check"></i>', 'falseValue' => '<i class="fa fa-times"></i>', 'nullValue' => '', 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'colegio_circ/_actions_column.twig',
            ]);
        }

        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('id', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => ColegioCirc::class,
            'hydrate' => Query::HYDRATE_ARRAY,
            'query' => function (QueryBuilder $builder) {                
                $builder
                    ->select('cc.id, col.colegio as colegio, cir.circunscripcion, cc.caracteresPermitidos, cc.estado, cc.visible')
                    ->addSelect('(SELECT COUNT(pro.id) FROM app\Entity\Profesional pro WHERE pro.colegio = cc.colegio and pro.circunscripcion = cc.circunscripcion) as cantProf')
                    ->from(ColegioCirc::class, 'cc')
                    ->join('cc.colegio', 'col')
                    ->join('cc.circunscripcion', 'cir');
            }
        ]);
    }

}
