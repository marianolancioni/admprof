<?php

namespace App\Controller;

use App\DataTables\UsuarioTableType;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UsuarioService;

/**
 * UsuarioController
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 * @Route("/usuario")
 */
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class UsuarioController extends AbstractController
{

    private $passwordHasher;
    private UsuarioService $_usuarioService;

    /**
     * Variable auxiliar para crear datatables
     *
     * @var [DataTableFactory]
     */
    protected $datatableFactory;

    public function __construct(DataTableFactory $datatableFactory, UserPasswordHasherInterface $passwordHasher, UsuarioService $usuarioService)
    {
        $this->passwordHasher = $passwordHasher;
        $this->datatableFactory = $datatableFactory;
        $this->_usuarioService = $usuarioService;
    }

    /**
     * @Route("/{filtroEstadoUsuario}", name="usuario_index", defaults={"filtroEstadoUsuario" = 1}, requirements={"filtroEstadoUsuario"="\d+"}, options={"expose"=true})
     */
    #[IsGranted("ROLE_ADMIN")]
    public function index(Request $request, int $filtroEstadoUsuario): Response
    {

        $table = $this->datatableFactory->createFromType(UsuarioTableType::class, array($filtroEstadoUsuario))->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('usuario/index.html.twig', ['datatable' => $table, 'filtroEstadoUsuario' => $filtroEstadoUsuario]);
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario->setFechaAlta(new \DateTime());
            $usuario->setTrustedVersion(0);

            $password = $this->_usuarioService->generatePassword();

            $usuario->setPassword($this->passwordHasher->hashPassword($usuario, $password));
            $usuario->setRoles(array_values($form->get("roles")->getData()));
            $entityManager->persist($usuario);
            $entityManager->flush();

            $this->addFlash('success-slow', 'Se ha creado el usuario: <b>' . $usuario->getUserIdentifier() . '</b>');
            
            // Envía correo con el password
            $email = $form->get("email")->getData();
            if ($this->_usuarioService->sendEmailPassword($email,  $usuario->getUserIdentifier(), $password)) {
                $this->addFlash('info-slow', 'Se ha enviado un correo con la clave del usuario a la dirección <strong>' . $email . '</strong>');
            } else {
                $this->addFlash('danger-slow-closable', 'No se ha podido enviar el correo con la clave del usuario a <strong>' . $email . '</strong>');
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            
            return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('usuario/' . $template, [
            'usuario' => $usuario,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    /**
     * @Route("/{id}/show", name="usuario_show", methods={"GET"})
     */
    #[IsGranted("ROLE_ADMIN")]
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods={"GET","POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado el usuario: ' . $usuario->getUserIdentifier());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';


        return $this->renderForm('usuario/' .$template, [
            'usuario' => $usuario,
            'form' => $form,
            ], new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));

    }

    /**
     * @Route("/{id}/delete", name="usuario_delete", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $usuario->getId(), $request->request->get('_token'))) {
            //No borro definitvamente el usuario sino que le seteo una fecha de baja (baja lógica)
            $usuario->setFechaBaja(new \DateTime('now'));
            $entityManager->flush();

            $this->addFlash('danger', 'Se ha dado de baja al usuario: ' . $usuario->getUserIdentifier());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/undelete", name="usuario_undelete", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function undelete(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('undelete' . $usuario->getId(), $request->request->get('_token'))) {
            $usuario->setFechaBaja(null);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha reactivado al usuario: ' . $usuario->getUserIdentifier());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/trust", name="usuario_trust_version", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function trustVersion(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('trust' . $usuario->getId(), $request->request->get('_token'))) {
            $usuario->setTrustedVersion($usuario->getTrustedVersion() + 1); // Incrementa el número de versión del token del usuario
            $entityManager->flush();

            $this->addFlash('success', 'Se ha forzado la autenticación 2FA para el usuario: ' . $usuario->getUserIdentifier());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/trustAll", name="usuario_trust_version_all", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function trustVersionAll(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('trustAll', $request->request->get('_token'))) {
            $this->_usuarioService->updateTrustVersionAllUser();
            $this->addFlash('success', 'Se ha forzado la autenticación 2FA para todos los usuarios.');

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/{id}/reset_password", name="usuario_reset_password", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function resetPassword(Request $request, Usuario $usuario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('undelete' . $usuario->getId(), $request->request->get('_token'))) {

            $password = $this->_usuarioService->generatePassword();

            $usuario->setPassword($this->passwordHasher->hashPassword($usuario, $password));
            $entityManager->persist($usuario);
            $entityManager->flush();

            // Envía correo con el password
            $email = $usuario->getEmail();
            if ($this->_usuarioService->sendEmailPassword($email,  $usuario->getUserIdentifier(), $password, 'RESET')) {
                $this->addFlash('success-slow', 'Se ha generado nueva clave para el usuario: <b>' . $usuario->getUserIdentifier() . '</b><br>Se ha enviado un correo con la clave del usuario a la dirección <strong>' . $email . '</strong>');
            } else {
                $this->addFlash('danger-slow-closable', 'No se ha podido enviar el correo con la clave del usuario a <strong>' . $email . '</strong>');
            }  

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }
}
