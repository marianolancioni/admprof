<?php 

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use App\Service\UsuarioLogAccionService;

 /**
 * SwitchUserSubscriber
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class SwitchUserSubscriber implements EventSubscriberInterface
{

    private UsuarioLogAccionService $_usuarioLogAccionService;

    public function __construct(UsuarioLogAccionService $usuarioLogAccionService)
    {
        $this->_usuarioLogAccionService = $usuarioLogAccionService;
    }

    public function onSwitchUser(SwitchUserEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->hasSession()) {
            if ($param = $request->get('_switch_user')){
                if ($param == '_exit') {
                    $this->_usuarioLogAccionService->impersonateOut($event);
                }
                $this->_usuarioLogAccionService->impersonateIn($event);

            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // constant for security.switch_user
            SecurityEvents::SWITCH_USER => 'onSwitchUser',
        ];
    }
}