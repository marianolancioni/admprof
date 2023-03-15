<?php 

namespace App\Service;

use App\Entity\UsuarioLogAccion;
use App\Repository\UsuarioLogAccionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;


 /**
 * UsuarioLogAccionService
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class UsuarioLogAccionService
{
    private UsuarioLogAccionRepository $_usuarioLogAccionRepository;
    private EntityManagerInterface $_entityManager;
    private Security $_security;

    /**
     * Constructor
     */
    public function __construct(UsuarioLogAccionRepository $usuarioLogAccionRepository, EntityManagerInterface $entityManager, Security $security)
    {
        $this->_usuarioLogAccionRepository = $usuarioLogAccionRepository;
        $this->_entityManager = $entityManager;
        $this->_security = $security;
    }

    /**
     * Audita Fecha y Hora de Login del Usuario a la Aplicación 
     */
    public function logIn($clienteIp) {
        $user = $this->_security->getUser();
        if ($user) {
            $usuarioLogAccion = new UsuarioLogAccion( $user, $clienteIp, 'Login', $user->getUltimoAcceso() );

            $this->_entityManager->persist($usuarioLogAccion);
            $this->_entityManager->flush();
        }
    }

    /**
     * Audita Fecha y Hora de Logout del Usuario de la Aplicación 
     */
    public function logOut($clienteIp) {
        if ($user = $this->_security->getUser() ) {
            $usuarioLogAccion = new UsuarioLogAccion( $user, $clienteIp, 'Logout' );

            $this->_entityManager->persist($usuarioLogAccion);
            $this->_entityManager->flush();
        }
    }    


    /**
     * Audita Fecha, Hora y Usuario al que se Suplanta al activar la funcionalidad de Suplantación de Usuario
     */
    public function impersonateIn($event) {

        $token = $event->getToken();
        $clienteIp = $event->getRequest()->getClientIp();

        if ($token && $clienteIp) {
            $impersonatorUser = null;
            $impersonatorToUser = $token->getUser();

            if ($token instanceof SwitchUserToken) {
                $impersonatorUser = $token->getOriginalToken()->getUser();

                $usuarioLogAccion = new UsuarioLogAccion( $impersonatorUser, $clienteIp, 'Inicia Suplantación de Usuario');
                $usuarioLogAccion->setImpersonateTo($impersonatorToUser); 
                    
                $this->_entityManager->persist($usuarioLogAccion);
                $this->_entityManager->flush();
    
            }
        }
    }   
    
    /**
     * Audita Fecha, Hora que finaliza una Suplantación de Usuario
     */
    public function impersonateOut($event) {

        $token = $event->getToken();
        $clienteIp = $event->getRequest()->getClientIp();

        if ($token && $clienteIp) {
            $impersonatorUser = $token->getUser();

            $usuarioLogAccion = new UsuarioLogAccion( $impersonatorUser, $clienteIp, 'Finaliza Suplantación de Usuario');
                
            $this->_entityManager->persist($usuarioLogAccion);
            $this->_entityManager->flush();
    
        }
    }   
    
    /**
     * Audita Fecha y Hora de Acciones específicas del Usuario sobre la Aplicación 
     */
    public function logAction($clienteIp, $action) {
        $user = $this->_security->getUser();
        if ($user && $clienteIp && $action) {
            $usuarioLogAccion = new UsuarioLogAccion( $user, $clienteIp, $action );

            $this->_entityManager->persist($usuarioLogAccion);
            $this->_entityManager->flush();
        }
    }    
}