<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use App\Service\UsuarioLogAccionService;

 /**
 * CustomLogoutListener
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class CustomLogoutListener
{
    private UsuarioLogAccionService $_usuarioLogAccionService;

    public function __construct(UsuarioLogAccionService $usuarioLogAccionService)
    {
        $this->_usuarioLogAccionService = $usuarioLogAccionService;
    }

    public function onLogout(LogoutEvent $logoutEvent):  void
    {
        // Audita Fecha y Hora de Login del Usuario a la Aplicación
        $this->_usuarioLogAccionService->logOut($logoutEvent->getRequest()->getClientIp());
    }
}