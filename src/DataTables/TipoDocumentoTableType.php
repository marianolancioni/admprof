<?php

namespace App\DataTables;

use App\Entity\TipoDocumento;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de Tipos de Documentos
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 */
class TipoDocumentoTableType extends AbstractController implements DataTableTypeInterface
{
    /**
     * Configuro las columnas y sus funcionamiento de la grilla de Tipo Documentos
     *
     * @param DataTable $dataTable
     * @param array $options
     * @return void
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->add('id', TextColumn::class, ['label' => 'Código', 'searchable' => false]); 
        $dataTable->add('tipoDocumento', TextColumn::class, ['label' => 'Tipo de Documento', 'searchable' => true, 'leftExpr' => "toUpper(u.tipoDocumento)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);  
            
        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'tipo_documento/_actions_column.twig',
            ]);
        }
    
        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('tipoDocumento', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => TipoDocumento::class,
            'query' => function (QueryBuilder $builder) {
              
                $builder
                    ->select('u')
                    ->from(TipoDocumento::class, 'u');
            }
        ]);
    }
}
