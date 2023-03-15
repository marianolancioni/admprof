<?php

namespace App\Controller;

use App\Entity\ColegioCirc;
use App\Form\ColegioCircType;
use App\Repository\ColegioCircRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use App\DataTables\ColegioCircTableType;

 /**
 * ColegioCircController
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
#[Route('/colegio/circ')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class ColegioCircController extends AbstractController
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

    #[Route('/', name: 'app_colegio_circ_index')]
    public function index(Request $request): Response
    {

        $table = $this->datatableFactory->createFromType(ColegioCircTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('colegio_circ/index.html.twig', ['datatable' => $table]);        
    }

    #[Route('/new', name: 'app_colegio_circ_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $colegioCirc = new ColegioCirc();
        $form = $this->createForm(ColegioCircType::class, $colegioCirc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($colegioCirc);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado el Nuevo Circunscripción/Colegio');

            if ($request->isXmlHttpRequest()) {
               return new Response(null, 204);
            }
      
            return $this->redirectToRoute('app_colegio_circ_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('colegio_circ/' . $template, [
            'colegio_circ' => $colegioCirc,
            'form' => $form,
            ], new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
             ));
    }

    #[Route('/{id}', name: 'app_colegio_circ_show', methods: ['GET'])]
    public function show(ColegioCirc $colegioCirc): Response
    {
        return $this->render('colegio_circ/show.html.twig', [
            'colegio_circ' => $colegioCirc,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_colegio_circ_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, ColegioCirc $colegioCirc, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ColegioCircType::class, $colegioCirc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado el Nuevo Circunscripción/Colegio');

            if ($request->isXmlHttpRequest()) {
               return new Response(null, 204);
            }

            return $this->redirectToRoute('app_colegio_circ_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';


        return $this->renderForm('colegio_circ/' . $template, [
            'colegio_circ' => $colegioCirc,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_colegio_circ_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, ColegioCirc $colegioCirc, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$colegioCirc->getId(), $request->request->get('_token'))) {
            $entityManager->remove($colegioCirc);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_colegio_circ_index', [], Response::HTTP_SEE_OTHER);
    }
}
