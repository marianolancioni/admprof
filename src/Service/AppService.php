<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

/**
 * Servicio utilizado para realizar acciones generales de los sistemas symfony del PJ
 * @todo Unificar en esta clase la librería UtilService
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class AppService
{
    private $_mailer;
    private Security $_security;

    public function __construct(MailerInterface $mailer, Security $security)
    {
        $this->_mailer = $mailer;
        $this->_security = $security;
    }

    /**
     * errorNotification
     * 
     * Envía un correo a lista de destinatarios definida en variable de entorno RECIPIENTS_MESSAGES_NOTIFICACIONS
     * con información del código de error, su traza completa y se complementa con datos del usuario y su dirección IP
     * 
     * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
     */
    public function errorNotification(Request $request)
    {
        
        $clienteIp = $request->getClientIp();
        $user = $this->getOriginalUser();
        $errorCode = $request->request->get('errorCode');
        $message = $request->request->get('message');
        $statusText = $request->request->get('statusText');
        $previus = $request->request->get('previus');
        $file = $request->request->get('file');
        $line = $request->request->get('line');
        $traceMessage = $request->request->get('traceMessage');
        $fromAdrress = $_ENV['MAIL_FROM'];            
        $destinatarios = explode(',', $_ENV['RECIPIENTS_MESSAGES_NOTIFICACIONS']);   

        try {
            $email = (new TemplatedEmail())->from($fromAdrress);

            foreach($destinatarios as $destinatario) {
                $email->addTo(trim($destinatario));
            }

            $email
                ->subject('Poder Judicial Santa Fe - Administrador de Profesionales - Error ' . $errorCode)
                ->htmlTemplate('app/error_notification.html.twig')
                ->context([
                        'expiration_date' => new \DateTime('+7 days'),
                        'clienteIp' => $clienteIp,
                        'user' => $user,
                        'errorCode' => $errorCode,
                        'message' => $message,
                        'previus' => $previus,
                        'file' => $file,
                        'line' => $line,
                        'statusText' => $statusText,
                        'traceMessage' => $traceMessage,
                    ]);
            $this->_mailer->send($email);   
         
            return true;                     
        } catch (\Symfony\Component\Mime\Exception\RfcComplianceException $e) {
            return false;
        }
    }

    /**
     * Obtiene el Usuario Actual
     * En caso de estar impersonando, obtiene el Usuario original (el que impersona y no el impersonado)
     */
    public function getOriginalUser() {

        $token = $this->_security->getToken();

        if ($token instanceof SwitchUserToken) {
            $user = $token->getOriginalToken()->getUser();
        } else {
            $user = $this->_security->getUser();
        }

        return $user;
    }

}

