<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;

/**
 * Servicio utilizado para enviar código de autenticación con un template de correo personalizado
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 */
class AuthCodeMailerService implements AuthCodeMailerInterface
{
    private $_mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
     * sendAuthCode
     * 
     * Envia correo con código de autenticación (SchebTwoFactorBundle)
     */   
    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();
        $fromAdrress = $_ENV['MAIL_FROM'];     
        $email = (new TemplatedEmail())->from($fromAdrress);

        $email->to($user->getEmailAuthRecipient());

        $email
            ->subject('Poder Judicial Santa Fe - Administrador de Profesionales - Código de Autenticación')
            ->htmlTemplate('app/send_auth_code.html.twig')
            ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'authCode' => $authCode,
                ]);
        $this->_mailer->send($email);   
         
    }    

}

