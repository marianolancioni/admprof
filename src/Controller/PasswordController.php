<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UsuarioRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UsuarioService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * PasswordController
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 * @IsGranted("ROLE_USER")
 */
class PasswordController extends AbstractController
{
    #[Route('/password', name: 'app_password')]
    public function claveUsuario(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UsuarioRepository $userRepository, UsuarioService $usuarioService): Response
    {
        $usuario = $this->getUser(); // Obtiene el usuario actualmente logeado
        
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordHasher->isPasswordValid($usuario, $form->get("oldPassword")->getData())) {
                $newPassword = $form->get("newPassword")->getData();
                $usuario->setPassword($passwordHasher->hashPassword($usuario, $newPassword));
                $entityManager->flush();

                $usuarioService->sendEmailPassword($usuario->getEmail(),  $usuario->getUserIdentifier(), $newPassword, 'CHANGE');

                $status = 'SUCCESSFUL';
            } else {
                $status = 'ERROR';
                $errorMessages = 'Clave incorrecta.';
            }
        }

        return $this->renderForm('password/change_password.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
            'status' => isset($status) ? $status : '',
            'errorMessages' => isset($errorMessages) ? $errorMessages : '',
            ]);
    }    
}
