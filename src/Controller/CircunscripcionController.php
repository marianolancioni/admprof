<?php

namespace App\Controller;

use App\DataTables\CircunscripcionTableType;
use App\Entity\Circunscripcion;
use App\Form\CircunscripcionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CircunscripcionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


 /**
 * CircunscripcionController
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 */
#[Route('/circunscripcion')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class CircunscripcionController extends AbstractController
{
    private $passwordHasher;

    /**
     * Variable auxiliar para crear datatables
     *
     * @var [DataTableFactory]
     */
    protected $datatableFactory;

    public function __construct(DataTableFactory $datatableFactory, UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->datatableFactory = $datatableFactory;
    }

    #[Route('/', name: 'app_circunscripcion_index')]
    public function index(Request $request): Response
    {
        $table = $this->datatableFactory->createFromType(CircunscripcionTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('circunscripcion/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_circunscripcion_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $circunscripcion = new Circunscripcion();
        $form = $this->createForm(CircunscripcionType::class, $circunscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($circunscripcion);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado la Circunscripcion con éxito');

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_circunscripcion_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('circunscripcion/' . $template, [
            'circunscripcion' => $circunscripcion,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    #[Route('/{id}', name: 'app_circunscripcion_show', methods: ['GET'])]
    public function show(Circunscripcion $circunscripcion): Response
    {
        return $this->render('circunscripcion/show.html.twig', [
            'circunscripcion' => $circunscripcion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_circunscripcion_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Circunscripcion $circunscripcion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CircunscripcionType::class, $circunscripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha editado la Circunscripcion');
            
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_circunscripcion_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('circunscripcion/' . $template, [
            'circunscripcion' => $circunscripcion,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    #[Route('/{id}', name: 'app_circunscripcion_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Circunscripcion $circunscripcion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$circunscripcion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($circunscripcion);
            $entityManager->flush();

            $this->addFlash('danger', 'Se ha dado de baja la Circunscripcion');

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('app_circunscripcion_index', [], Response::HTTP_SEE_OTHER);
    }
}
