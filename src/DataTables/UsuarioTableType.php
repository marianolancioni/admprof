<?php

namespace App\DataTables;

use App\Entity\Usuario;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TableType para datatables de grilla de Usuarios
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 */
class UsuarioTableType extends AbstractController implements DataTableTypeInterface
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
        $dataTable->add('id', TextColumn::class, ['label' => 'Id', 'searchable' => false]);
        $dataTable->add('username', TextColumn::class, ['label' => 'Usuario', 'searchable' => true, 'leftExpr' => "toUpper(u.username)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('apenom', TextColumn::class, ['label' => 'Apellido y Nombres', 'orderable' => true, 'searchable' => true, 'leftExpr' => "toUpper(u.apellido)", 'field' => 'u.apellido',
            'render' => function ($value, $context) {
                return $context->getApeNom();
            },
            'rightExpr' => function ($value) {
                return '%' . strtoupper($value) . '%';
            }
        ]);
        $dataTable->add('rolesUsuario', TextColumn::class, ['label' => 'Roles', 'searchable' => false, 'render' => function ($value, $context) {
            return str_replace('ROLE_', '', implode(', ', $context->getRoles()));
        }]);
        $dataTable->add('colegio', TextColumn::class, ['label' => 'Colegio', 'searchable' => false, 'field' => 'colegio.colegio', 'leftExpr' => "toUpper(colegio.colegio)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('circunscripcion', TextColumn::class, ['label' => 'Circunscripción', 'searchable' => false, 'field' => 'circunscripcion.circunscripcion', 'leftExpr' => "toUpper(circunscripcion.circunscripcion)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);
        $dataTable->add('fechaAlta', DateTimeColumn::class, ['label' => 'Alta', 'format' => 'd/m/y', 'className' => 'text-center', 'searchable' => false, 'operator' => 'LIKE', 'leftExpr' => "toChar(u.fechaAlta, 'DD/MM/YY')", 'rightExpr' => function ($value) {
            return '%' . $value . '%';
        }]);
        $dataTable->add('fechaBaja', DateTimeColumn::class, ['label' => 'Baja', 'format' => 'd/m/y', 'className' => 'text-center', 'searchable' => false, 'operator' => 'LIKE', 'leftExpr' => "toChar(u.fechaBaja, 'DD/MM/YY')", 'rightExpr' => function ($value) {
            return '%' . $value . '%';
        }]);
        $dataTable->add('ultimoAcceso', DateTimeColumn::class, ['label' => 'Último Acceso', 'format' => 'd/m/y', 'className' => 'text-center', 'searchable' => false, 'operator' => 'LIKE', 'leftExpr' => "toChar(u.ultimoAcceso, 'DD/MM/YY')", 'rightExpr' => function ($value) {
            return '%' . $value . '%';
        }]);
        $dataTable->add('cantidadAccesos', TextColumn::class, ['label' => 'Accesos', 'className' => 'text-center', 'searchable' => false]);
        
        // COLUMNA NOMBRE DUPLICADA OCULTA => PARA PODER RELIZAR BÚSQUEDA INDIVIDUAL POR NOMBRE
        $dataTable->add('nombre', TextColumn::class, ['searchable' => true, 'visible' => false, 'leftExpr' => "toUpper(u.nombre)", 'rightExpr' => function ($value) {
            return '%' . strtoupper($value) . '%';
        }]);

        // ACCIONES
        if ($this->isGranted(('ROLE_ADMIN'))) {
            $dataTable->add('Modal', TwigColumn::class, [
                'label' => 'Acciones', 
                'className' => 'buttons',
                'template' => 'usuario/_actions_column.twig',
            ]);
        }

        // ORDEN DE LA GRILLA
        $dataTable->addOrderBy('username', DataTable::SORT_ASCENDING);

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Usuario::class,
            'query' => function (QueryBuilder $builder) use ($options) {

                if (count($options) > 0 && $options[0] > 0) {
                    // Busco por estado del usuario
                    if ($options[0] == 1)  // Activos
                        $filter = 'IS NULL';

                    if ($options[0] == 2)  // Dados de Baja
                        $filter = 'IS NOT NULL';

                    $builder
                        ->select('u')
                        ->from(Usuario::class, 'u')
                        ->leftjoin('u.colegio', 'colegio')
                        ->leftjoin('u.circunscripcion', 'circunscripcion')
                        ->where('u.fechaBaja ' . $filter);
                } else {
                    $builder
                        ->select('u')
                        ->from(Usuario::class, 'u')
                        ->leftjoin('u.colegio', 'colegio')
                        ->leftjoin('u.circunscripcion', 'circunscripcion');
                }
            }
        ]);
    }
}
