<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use App\Service\AuditService;

/**
 * AuditSubscriber
 * 
 * Clase encargada de escuchar eventos de entidades para setear usuarios
 * 
 * @author Juani Alarcón <jialarcon@justiciasantafe.gov.ar>
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class AuditSubscriber implements EventSubscriber
{
    // Lista de Entidades a Auditar
    private $entitiesAudit;

    private $_security;

    /**
     * Servicio de Auditoría
     */
    protected $_auditoriaService;

    public function __construct(Security $security, AuditService $auditService)
    {
        $this->_security = $security;
        $this->_auditoriaService = $auditService;
        $this->entitiesAudit = $this->_auditoriaService->getEntitiesAuditables();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postRemove,
        ];
    }

    /**
     * Método invocado antes de crear una entidad
     * @param LifecycleEventArgs
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($this->isOfValidClass($entity))
        {
            $entity->setLastUserAppId($this->getUserId());
        }
    }

    /**
     * Método invocado antes de actualizar una entidad
     * @param LifecycleEventArgs
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($this->isOfValidClass($entity))
        {
            $entity->setLastUserAppId($this->getUserId());
        }
    }

    /**
     * Método invocado antes de borrar una entidad.
     * @param LifecycleEventArgs
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        // Guardo el id de la entidad para auditarlo luego de borrar
        $entity->setStoreId($entity->getId());
    }

    /**
     * Método invocado luego de borrar una entidad
     * @param LifecycleEventArgs
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if ($this->isOfValidClass($args->getObject()))
        {
            $entity = $args->getObject();
            $entityName = (new \ReflectionClass($entity))->getShortName();
            $this->_auditoriaService->stampUserIdDelete($entityName, $entity->getStoreId(), $this->getUserId());
        }
    }

    protected function isOfValidClass($obj)
    {
        foreach ($this->entitiesAudit as $className) {
            if (is_a($obj, $className)) {
                return true;
            }
        }
    
        return false;
    }    

    /**
     * Obtiene ID del Usuario. En caso de que esté activa la impersonalización retorna el ID del usuario que impersonaliza.
     */
    protected function getUserId()
    {

        $token = $this->_security->getToken();

        if ($token instanceof SwitchUserToken) {
            return $token->getOriginalToken()->getUser()->getId();
        }

        // Verifica si el usuario está logeado
        if ($this->_security->getUser()) {
            return $this->_security->getUser()->getId();
        }
        
        return 0; // Usuario no logeado

    }  

}