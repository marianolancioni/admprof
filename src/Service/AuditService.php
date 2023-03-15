<?php

namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;

/**
 * AuditService
 * 
 * Servicio utilizado para realizar acciones generales de Auditoría de Datos
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class AuditService
{

    protected $_entityManager;
    protected $entitiesAuditables;
    protected $entitiesList;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_entityManager = $entityManager;
        
        $this->entitiesAuditables = $this->obtainEntitiesAuditables();
        $this->entitiesList = $this->obtainEntitiesList();
    }

    /**
     * Ante un evento de DELETE, graba en el evento de auditoría asociado el ID del Usuario (a nivel de Aplicación) que generó la acción
     * 
     * @param String nombreEntidad
     * @param int idEntidad
     * @param int idUsuario
     */
    public function stampUserIdDelete(string $nombreEntidad, int $idEntidad, int $idUsuario)
    {
        //TODO: Se debería manejar una excepción si el retorno es distinto a 1. En caso de ser -1 se produjo un error. En caso de ser 0 no se actualizó ningún registro.

        $sql = 'SELECT auditoria."acp_audit_userid_delete"(' . "'" . $nombreEntidad . "', " . $idEntidad . "," . $idUsuario . ")";

        $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();

        return $result;
    }

    /**
     * Obtiene Entidades Auditables a partir de las definidas en el parámetro ENTIDADES_A_AUDITAR del archivo .env.
     * Este método se ejecuta por única vez al momento de instanciar la clase
     * 
     */
    private function obtainEntitiesAuditables() {

        try {
            $entitiesAuditables = explode(',', $_ENV['ENTIDADES_A_AUDITAR']);

            foreach ($entitiesAuditables as &$entitieAuditable) {
                $entitieAuditable = 'App\\Entity\\' .  trim($entitieAuditable);
            }
    
            return $entitiesAuditables;    
        }
        catch (\ErrorException $e) {
            return []; // Retorna un array vacío si no se encuentra definida la variable de entorno
        }


    }

    /**
     * Obtiene lista de Entidades gestionadas por Doctrine
     * Este método se ejecuta por única vez al momento de instanciar la clase
     * 
     */
    private function obtainEntitiesList()
    {
        $entities= [];

        $metas = $this->_entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($metas as $meta) {
            $entities[] = $meta->getName();
        }

        return $entities;
    }

    /**
     * Obtiene nombre la Tabla en Base de Datos de una Entidad
     * 
     * @param String $entityName
     * @return String Nombre de Tabla en Base de Datos asociada a la Entidad
     */
    public function getTableName(string $entityName)
    {

        try {
            $metaData = $this->_entityManager->getMetadataFactory()->getMetadataFor($entityName);
            return $metaData->table['name'];
        }
        catch (\Doctrine\Persistence\Mapping\MappingException $e) {
            return '';
        }

    }

    /**
     * Verifica si una entidad esta preparada para Auditoría
     * (se verifica la existencia de las propiedades lastUserAppId y storeId y de los métodos setLastUserAppId(), getStoreId() y setStoreId())
     * 
     * @param String $entityName
     * @return Boolean
     */
    public function isReadyToAudit(string $entityName)
    {
        try {
            $metaData = $this->_entityManager->getMetadataFactory()->getMetadataFor($entityName)->getReflectionClass();

            return (
                    $metaData->getProperty('lastUserAppId') &&
                    $metaData->getProperty('storeId') &&
                    $metaData->getMethod('setLastUserAppId') &&
                    $metaData->getMethod('getStoreId') &&
                    $metaData->getMethod('setStoreId') 
                );
        }
        catch (\Doctrine\Persistence\Mapping\MappingException $e) {
            return false;
        }
        catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Obtiene información de tamaño y cantidad de registros de una tabla de la Base de Datos
     * 
     * @return Array tableInfo
     */
    public function getTableInfo(String $tableName)
    {

        $sql = "SELECT count(*) as registros,
                    (SELECT pg_size_pretty( pg_total_relation_size('$tableName') )) as size
            FROM $tableName";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchAssociative();

            return $result;
        }
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return false;
        }
    }    

    /**
     * Obtiene primer y último registro de un campo de una tabla de la Base de Datos
     * 
     * @param String $tableName
     * @param String $fieldName
     * @return Array [first, last]
     */
    public function getValueFirstLastFieldTable(String $tableName, String $fieldName)
    {

        $sql = "SELECT  (SELECT $fieldName FROM $tableName WHERE id = (SELECT MIN(id) FROM $tableName)) as First, 
	                    (SELECT $fieldName FROM $tableName WHERE id = (SELECT MAX(id) FROM $tableName)) as Last";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchAssociative();

            return $result;
        }
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return 0;
        }
    }     


    /**
     * Determina si la entidad es Auditada
     * 
     * @param String $entityName
     * @return Boolean 
     */
    public function isAudited(string $entityName)
    {

        if (array_search($entityName, $this->getEntitiesAuditables() ) !== false) {
            return true;
        }

        return false;

    }


    /**
     * Obtiene nombre la Tabla de Auditoria asociada a la Entidad
     * 
     * @param String $entityName
     * @return String Nombre de Tabla de Auditoria en Base de Datos asociada a la Entidad
     */
    public function getAuditTableName(string $entityName)
    {

        $schemaName = 'auditoria';
        $nameSufix = '_audit';
        $auditTableName = $schemaName . '.' . $entityName . $nameSufix;

        return $auditTableName;

    }

    /**
     * Verifica si existe tabla de datos de Auditoria asociada a la Entidad
     * 
     * @param String $entityName
     * @return Boolean
     */
    public function isExistTableAudit(string $entityName)
    {

        $nameSufix = '_audit';
        $auditTableName = $entityName . $nameSufix;

        $sql = "SELECT count(*) FROM information_schema.columns 
                WHERE table_name ilike '$auditTableName'";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();

            if ($result) 
                return true;
            else
                return false;
        }
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return false;
        }        
       
    }    

    /**
     * Verifica si existen los trigger de auditoría a nivel de Base de Datos para la Entidad
     * 
     * @param String $entityName
     * @return Boolean
     */
    public function isExistTriggerAudit(string $entityName)
    {

        $nameSufix = '_audit_upd';
        $auditTriggerName = $entityName . $nameSufix;

        $sql = "SELECT count(*) FROM information_schema.triggers
                WHERE trigger_name ilike '$auditTriggerName'";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();

            if ($result == 3) // Verifico que existan los trigger de INSERT, DELETE y UPDATE
                return true;
            else
                return false;
        }
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return false;
        }        
       
    }    

    /**
     * Obtiene Configuración de Auditoría por cada Entidad a nivel de Aplicación
     * 
     * @return Array
     */
    public function getConfig(): Array
    {
        $auditConfig = [];
        $i = 0;

        foreach($this->getEntitiesList() as $entity) {
            $tableName = $this->getTableName($entity);
            $tableInfo = $this->getTableInfo($tableName);    
            $existTableAudit = $this->isExistTableAudit($tableName);
            $existTriggerAudit = $this->isExistTriggerAudit($tableName);
            $isAuditable = $this->isReadyToAudit($entity);
            $isAudited = $this->isAudited($entity);

            if ($tableInfo) {
                $auditConfig[$i]['id'] = $i;
                $auditConfig[$i]['entidad'] = substr($entity,11); //Me quedo con el nombre de la entidad (sin el App/Entity)
                $auditConfig[$i]['tableName'] = $tableName;
                $auditConfig[$i]['cntRegister'] = $tableInfo['registros'];
                $auditConfig[$i]['size'] = $tableInfo['size'];
                $auditConfig[$i]['isAuditable'] = $isAuditable;
                $auditConfig[$i]['state'] = ''; // Auxiliar donde se implementará lógica de estado (Auditoría inactiva, activa, pausada)

                if ($isAudited) {
                    $auditConfig[$i]['isAudited'] = true;
                }
                else {
                    $auditConfig[$i]['isAudited'] = false;
                }

                if ($isAudited && !$isAuditable) {
                    $auditConfig[$i]['isAudited'] = null;   // Retorna null para mostrar advertencia visual que está configurada
                                                            // en el .env pero no implementa los métodos de auditoría
                }               

                $auditConfig[$i]['existTableAudit'] = $existTableAudit;
                $auditConfig[$i]['existTriggerAudit'] = $existTriggerAudit;

                if ($existTableAudit) {
                    $tableInfo = $this->getTableInfo($this->getAuditTableName($tableName)); 
                    $firstLast = $this->getValueFirstLastFieldTable($this->getAuditTableName($tableName), 'audit_timestamp');

                    $auditConfig[$i]['cntRegisterAudit'] = $tableInfo['registros'];
                    $auditConfig[$i]['sizeAudit'] = $tableInfo['size'];
                    $auditConfig[$i]['first'] = $firstLast['first'] ?? null;
                    $auditConfig[$i]['last'] = $firstLast['last'] ?? null;
                }
                else {
                    $auditConfig[$i]['cntRegisterAudit'] = '';
                    $auditConfig[$i]['sizeAudit'] = '';
                    $auditConfig[$i]['first'] = null;
                    $auditConfig[$i]['last'] = null;    
                }    
            }
            $i++;
        }
        
        return $auditConfig;
    }  
    
    /**
     * Activa la auditoría a nivel de Base de Datos para la Entidad
     * 
     * @param String $tableName
     * @return String
     */
    public function activate(string $tableName)
    {

        $schemaAuditName = 'auditoria';
        $schemaDataName = 'public';

        $sql = "SELECT $schemaAuditName.acp_audit_table('$schemaDataName.$tableName')";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();
            return '';
        }
        catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return 'La tabla a Auditar no existe!';
        }        
        catch (\Doctrine\DBAL\Exception\TableExistsException $e) {
            return 'La tabla de Auditoría ya existe!';
        }        
        catch (\Doctrine\DBAL\Exception\DriverException  $e) {
            return 'Ya existe un objeto similar al que se intentó crear!<br>Verifique que no exista la tabla o trigger de Auditoría para la tabla.';
        }             
    }     

    /**
     * Detiene la auditoría a nivel de Base de Datos para la Entidad
     * 
     * @param String $tableName
     * @return String
     */
    public function pause(string $tableName)
    {

        $schemaAuditName = 'auditoria';
        $schemaDataName = 'public';
        $triggerName = $tableName . '_audit_upd';

        $sql = "DROP TRIGGER IF EXISTS $triggerName ON $schemaDataName.$tableName";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();
            return '';
        }
        catch (\Doctrine\DBAL\Exception\DriverException  $e) {
            return 'Ya existe un objeto similar al que se intentó crear!<br>Verifique que no exista la tabla o trigger de Auditoría para la tabla.<hr>' . $e;
        }             
    }     

    /**
     * Reanuda la auditoría a nivel de Base de Datos para la Entidad
     * 
     * @param String $tableName
     * @return String
     */
    public function resume(string $tableName)
    {

        $schemaAuditName = 'auditoria';
        $schemaDataName = 'public';
        $triggerName = $tableName . '_audit_upd';

        $sql = "CREATE TRIGGER $triggerName
                BEFORE INSERT OR DELETE OR UPDATE 
                ON $schemaDataName.$tableName
                FOR EACH ROW
                    EXECUTE FUNCTION $schemaAuditName.$triggerName()";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql)
                        ->executeQuery()
                        ->fetchOne();
            return '';
        }
        catch (\Doctrine\DBAL\Exception\DriverException  $e) {
            return $e; //'Ya existe un objeto similar al que se intentó crear!<br>Verifique que no exista la tabla o trigger de Auditoría para la tabla.';
        }             
    }     

    /**
     * Borra tabla de Auditoría a nivel de Base de Datos para la Entidad
     * 
     * @param String $tableName
     * @return String
     */
    public function delete(string $tableName)
    {

        $schemaAuditName = 'auditoria';
        $tableName = $tableName . '_audit';
        $triggerName = $tableName . '_id_seq';
        
        $sql1 = "DROP TABLE IF EXISTS $schemaAuditName.$tableName";
        $sql2 = "DROP SEQUENCE IF EXISTS $schemaAuditName.$triggerName";

        try {
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql1)
                        ->executeQuery()
                        ->fetchOne();
            $result =  $this->_entityManager
                        ->getConnection()
                        ->prepare($sql2)
                        ->executeQuery()
                        ->fetchOne();


            return '';
        }
        catch (\Doctrine\DBAL\Exception\DriverException  $e) {
            return 'Se ha producido un error.<hr><small>' . $e . '</small>';
        }             
    }    
    
    /**
     * Obtiene eventos de auditoría sobre una tabla
     * 
     * @param String $tableName
     * @return Array
     */
    public function getAuditoria($tableName)
    {

        $sql = "SELECT a.id, a.audit_timestamp, a.audit_iduserapp, a.audit_identity, 
                    CASE audit_action
                        WHEN 'I' THEN 'Alta'
                        WHEN 'U' THEN 'Modificación'
                        WHEN 'D' THEN 'Baja'
                    ELSE 
                        ''
                    END as audit_action,
                    a.audit_old,
                    a.audit_new,
                    u.username
                FROM auditoria." . $tableName . "_audit a INNER JOIN public.usuario u ON u.id = a.audit_iduserapp";

        $result = $this->_entityManager
                    ->getConnection()
                    ->prepare($sql)
                    ->executeQuery()
                    ->fetchAllAssociative();

        return $result;
    
    }     

    /**
     * Obtiene Lista de Entidades Auditables a partir de las definidas en el parámetro ENTIDADES_A_AUDITAR del archivo .env.
     * 
     * @return Array
     */
    public function getEntitiesAuditables(): Array
    {
        return $this->entitiesAuditables;
    }

    /**
     * Obtiene lista de Entidades gestionadas por Doctrine
     * 
     * @return Array
     */
    public function getEntitiesList(): Array
    {
        return $this->entitiesList;
    }


}
