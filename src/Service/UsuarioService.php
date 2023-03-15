<?php 

namespace App\Service;

use App\Repository\UsuarioRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;


 /**
 * UsuarioService
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class UsuarioService
{
    private UsuarioRepository $_usuarioRepository;  // Repository del Usuario para almacenar fecha del login e incrementar cant. de accesos
    protected MailerInterface $_mailer; 
    protected EntityManagerInterface $_entityManager;

    /**
     * Constructor
     */
    public function __construct(MailerInterface $mailer, UsuarioRepository $usuarioRepository, EntityManagerInterface $entityManager)
    {
        $this->_mailer = $mailer;
        $this->_usuarioRepository = $usuarioRepository;
        $this->_entityManager = $entityManager;
    }

    /**
     * Verifica que exista un usuario
     */
    public function isUser(String $username) {
        return ($this->_usuarioRepository->findOneBy(['username' => $username]) ? true : false);
    }

    /**
     * Verifica que existe un usuario y el mismo no se encuentre dado de baja
    */
    public function isUserEnabled(String $username) {
        $user = $this->_usuarioRepository->findOneBy(['username' => $username]);

        return ($user && $user->getFechaBaja() != null ? true : false);
    }

    /**
     * Actualiza fecha de última conexión e incrementa el contador de conexiones
    */
    public function updateLoggingInformation(String $username) {
        $user = $this->_usuarioRepository->findOneBy(['username' => $username]);
        if ($user) {
            $this->_usuarioRepository->actualizarLogueoUsuario($user);
        }
    }

    /**
     * Genera password del Usuario
     */
    public function generatePassword(): string
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
        ;

        $password = $generator->generatePassword();

        return $password;
    }

    /**
     * Notifica por Correo password al Usuario
     */
    public function sendEmailPassword(String $email, String $usuario, String $password, String $action = 'NEW') {

        if ($action == 'NEW')
            $mailTemplate = 'usuario/mail_new.html.twig';

        if ($action == 'RESET' || $action == 'CHANGE')
            $mailTemplate = 'usuario/mail_reset.html.twig';

        try {
            $fromAdrress = $_ENV['MAIL_FROM'];
            $email = (new TemplatedEmail())
                        ->from($fromAdrress)
                        ->to($email)
                        ->subject('Poder Judicial Santa Fe - Administrador de Profesionales')
                        ->htmlTemplate($mailTemplate)
                        ->context([
                            'expiration_date' => new \DateTime('+7 days'),
                            'usuario' => $usuario,
                            'password' => $password,
                            'action' => $action,
                        ]);
            $this->_mailer->send($email);   
            return true;                     
        } catch (\Symfony\Component\Mime\Exception\RfcComplianceException $e) {
            return false;
        }
    }

    /**
     * Incrementa el número de versión del token de confianza de todos los usuarios
     * (fuerza autenticación 2FA en el próximo login de cada usuario)
    */
    public function updateTrustVersionAllUser() {
        $users = $this->_usuarioRepository->findAll();

        if ($users) {
            foreach ($users as $usuario) {
                $usuario->setTrustedVersion($usuario->getTrustedVersion() + 1); // Incrementa el número de versión del token del usuario
                $this->_entityManager->flush();
            }
        }
    }    

}