<?php

namespace App\Controller;

use App\DataTables\EstadoTableType;
use App\Entity\Estado;
use App\Form\EstadoType;
use App\Repository\EstadoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

 /**
 * EstadoController
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 */
#[Route('/estado')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class EstadoController extends AbstractController
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

    #[Route('/', name: 'app_estado_index')]
    public function index(Request $request): Response
    {
        $table = $this->datatableFactory->createFromType(EstadoTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('estado/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_estado_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $estado = new Estado();
        $form = $this->createForm(EstadoType::class, $estado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($estado);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado el Estado con éxito');

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_estado_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        
        return $this->renderForm('estado/' . $template, [
            'estado' => $estado,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    #[Route('/{id}', name: 'app_estado_show', methods: ['GET'])]
    public function show(Estado $estado): Response
    {
        return $this->render('estado/show.html.twig', [
            'estado' => $estado,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_estado_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Estado $estado, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EstadoType::class, $estado);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Se ha editado el Estado');
            
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_estado_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('estado/' .$template, [
            'estado' => $estado,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    #[Route('/{id}', name: 'app_estado_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Estado $estado, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estado->getId(), $request->request->get('_token'))) {
            $entityManager->remove($estado);
            $entityManager->flush();
            
            $this->addFlash('danger', 'Se ha dado de baja el Estado');

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('app_estado_index', [], Response::HTTP_SEE_OTHER);
    }
}
