<?php

namespace App\Controller;

use App\Entity\Localidad;
use App\Form\LocalidadType;
use App\Repository\LocalidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use App\DataTables\LocalidadTableType;

 /**
 * LocalidadController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
#[Route('/localidad')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class LocalidadController extends AbstractController
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


    #[Route('/', name: 'app_localidad_index')]
    public function index(Request $request): Response
    {
        $table = $this->datatableFactory->createFromType(LocalidadTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('localidad/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_localidad_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $localidad = new Localidad();
        $form = $this->createForm(LocalidadType::class, $localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($localidad);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado la Localidad');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
             }

            
            return $this->redirectToRoute('app_localidad_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('localidad/' . $template, [
            'localidad' => $localidad,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_localidad_show', methods: ['GET'])]
    public function show(Localidad $localidad): Response
    {
        return $this->render('localidad/show.html.twig', [
            'localidad' => $localidad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_localidad_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Localidad $localidad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LocalidadType::class, $localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado la Localidad');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
             }

            return $this->redirectToRoute('app_localidad_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->renderForm('localidad/' . $template, [
            'localidad' => $localidad,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_localidad_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Localidad $localidad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$localidad->getId(), $request->request->get('_token'))) {
            $entityManager->remove($localidad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_localidad_index', [], Response::HTTP_SEE_OTHER);
    }
}
