<?php

namespace App\DataTables;

use App\Entity\Estado;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de Estados
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 */
class EstadoTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de Estados
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        /*El estado representa un código númerico que puede ser >= 0 (EJ=10)*/
        $dataTable->add('estado', TextColumn::class, ['label' => 'Código', 'searchable' => false, 'leftExpr' => "toUpper(e.estado)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('estadoProfesional', TextColumn::class, ['label' => 'Estado Profesional', 'searchable' => true, 'leftExpr' => "toUpper(e.estadoProfesional)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'estado/_actions_column.twig',
            ]);
        }

        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('estado', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Estado::class,
            'query' => function (QueryBuilder $builder) {
                $builder
                    ->select('e')
                    ->from(Estado::class, 'e');

            }
        ]);
    }

}
