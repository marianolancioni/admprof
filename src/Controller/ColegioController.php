<?php

namespace App\Controller;

use App\DataTables\ColegioTableType;
use App\Entity\Colegio;
use App\Form\ColegioType;
use App\Repository\ColegioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;

 /**
 * ColegioController
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 * 
 */
#[Route('/colegio')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class ColegioController extends AbstractController
{
    /**
     * Variable auxiliar para crear datatables
     *
     * @var [DataTableFactory]
     */
    protected $datatableFactory;

    public function __construct(DataTableFactory $datatableFactory)
    {
        $this->datatableFactory = $datatableFactory;
    }

    #[Route('/', name: 'app_colegio_index')]
    public function index(Request $request): Response
    {
            $table = $this->datatableFactory->createFromType(ColegioTableType::class, array())->handleRequest($request);
            if ($table->isCallback()) {
                return $table->getResponse();
            }
            return $this->render('colegio/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_colegio_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $colegio = new Colegio();
        $form = $this->createForm(ColegioType::class, $colegio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($colegio);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado el Nuevo Colegio');

            if ($request->isXmlHttpRequest()) {
               return new Response(null, 204);
            }

            return $this->redirectToRoute('app_colegio_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('colegio/' . $template, [
            'colegio' => $colegio,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));

    }

    #[Route('/{id}', name: 'app_colegio_show', methods: ['GET'])]
    public function show(Colegio $colegio): Response
    {
        return $this->render('colegio/show.html.twig', [
            'colegio' => $colegio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_colegio_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Colegio $colegio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ColegioType::class, $colegio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado el Colegio');

            if ($request->isXmlHttpRequest()) {
               return new Response(null, 204);
            }


            return $this->redirectToRoute('app_colegio_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->renderForm('colegio/' . $template, [
            'colegio' => $colegio,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_colegio_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Colegio $colegio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$colegio->getId(), $request->request->get('_token'))) {
            $entityManager->remove($colegio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_colegio_index', [], Response::HTTP_SEE_OTHER);
    }
}
