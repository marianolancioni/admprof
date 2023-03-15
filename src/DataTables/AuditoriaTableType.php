<?php

namespace App\DataTables;

use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Omines\DataTablesBundle\Adapter\ArrayAdapter;


/**
 * TableType para datatables de grilla de Usuarios
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class AuditoriaTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de usuarios
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable
            ->add('id', TextColumn::class, ['visible' => false, 'label' => 'Id', 'searchable' => false])
            ->add('entidad', TextColumn::class, ['label' => 'Entidad de Datos', 'searchable' => true, 'orderable' => true])
            ->add('tableName', TextColumn::class, ['visible' => false, 'label' => 'Entidad de Datos', 'searchable' => true, 'orderable' => true])
            ->add('cntRegister', NumberColumn::class, ['label' => 'Cant. Registros', 'className' => 'text-end', 'orderable' => true])
            ->add('size', TextColumn::class, ['label' => 'Tamaño', 'className' => 'text-end', 'orderable' => true])
            ->add('Estado', TextColumn::class, ['label' => 'Estado', 'className' => 'text-center', 'orderable' => false, 'render' => function ($value, $context) {
                if (!$context['isAudited'] || !$context['isAuditable'] )
                    return '<i class="fa-solid fa-circle-exclamation text-warning"></i><br><small class="text-secondary">No Activada</small>'; // Sin Auditoría
                if ($context['isAudited'] && $context['isAuditable'] && $context['existTableAudit'] && !$context['existTriggerAudit'] )
                    return '<i class="fa-solid fa-circle-pause text-secondary"></i><br><small class="text-primary">Pausada</small>'; // Pausada
                if ($context['isAudited'] && $context['isAuditable'] && $context['existTableAudit'] && $context['existTriggerAudit'] )
                    return '<i class="fa-solid fa-circle-play text-primary"></i><br><small class="text-primary">Activa</small>'; // Activada
                if ($context['isAudited'] && $context['isAuditable'] && !$context['existTableAudit'] && !$context['existTriggerAudit'])
                    return '<i class="fa-solid fa-circle-plus text-secondary"></i><br><small class="text-secondary">No Activada</small>'; // Activar

                return '';
              }])
            ->add('cntRegisterAudit', NumberColumn::class, ['label' => 'Cant. Registros Auditoría', 'className' => 'text-end', 'orderable' => true, 'render' => function ($value, $context) {
                return $value ? $value : '';
            }])
            ->add('sizeAudit', TextColumn::class, ['label' => 'Tamaño Auditoría', 'className' => 'text-end', 'orderable' => true])
            ->add('first', DateTimeColumn::class, ['label' => '1er. Reg. Auditoría', 'className' => 'text-center', 'orderable' => true, 'format' => 'd/m/Y H:i'])
            ->add('last', DateTimeColumn::class, ['label' => 'Ult. Reg. Auditoría', 'className' => 'text-center', 'orderable' => true, 'format' => 'd/m/Y H:i'])
            ->add('isAudited', BoolColumn::class, ['visible' => false,'label' => 'Configurada para Auditar', 'searchable' => false, 'className' => 'text-center', 'orderable' => true, 'trueValue' => '<i class="fa-solid fa-sun text-success"></i>', 'falseValue' => '<i class="fa-regular fa-sun text-secondary"></i>', 'nullValue' => '<i class="fa-regular fa-sun text-warning"></i>'])
            ->add('isAuditable', BoolColumn::class, ['visible' => false, 'label' => 'Es auditable', 'searchable' => false, 'className' => 'text-center', 'orderable' => true, 'trueValue' => '<i class="fa-solid fa-sun text-success"></i>', 'falseValue' => '<i class="fa-regular fa-sun text-secondary"></i>', 'nullValue' => ''])
            ->add('existTableAudit', BoolColumn::class, ['visible' => false,'label' => 'Tabla Auditoría', 'searchable' => false, 'className' => 'text-center', 'orderable' => true, 'trueValue' => '<i class="fa-solid fa-sun text-success"></i>', 'falseValue' => '<i class="fa-regular fa-sun text-secondary"></i>', 'nullValue' => ''])
            ->add('existTriggerAudit', BoolColumn::class, ['visible' => false,'label' => 'Trigger de Auditoría', 'searchable' => false, 'className' => 'text-center', 'orderable' => true, 'trueValue' => '<i class="fa-solid fa-sun text-success"></i>', 'falseValue' => '<i class="fa-regular fa-sun text-secondary"></i>', 'nullValue' => '']);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'auditoria/_actions_column.twig',
            ]);
        }

        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('tableName', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ArrayAdapter::class, $options[0] );
    }
}
