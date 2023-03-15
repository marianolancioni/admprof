<?php

namespace App\Controller;

use App\Entity\Provincia;
use App\Form\ProvinciaType;
use App\Repository\ProvinciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use App\DataTables\ProvinciaTableType;

 /**
 * ProvinciaController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
#[Route('/provincia')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class ProvinciaController extends AbstractController
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


    #[Route('/', name: 'app_provincia_index')]
    public function index(Request $request): Response
    {
        $table = $this->datatableFactory->createFromType(ProvinciaTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('provincia/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_provincia_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $provincia = new Provincia();
        $form = $this->createForm(ProvinciaType::class, $provincia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($provincia);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado la Provincia: …');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
             }

            return $this->redirectToRoute('app_provincia_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('provincia/' . $template, [
            'provincia' => $provincia,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_provincia_show', methods: ['GET'])]
    public function show(Provincia $provincia): Response
    {
        return $this->render('provincia/show.html.twig', [
            'provincia' => $provincia,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_provincia_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Provincia $provincia, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProvinciaType::class, $provincia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado la Provincia: …');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
             }

            return $this->redirectToRoute('app_provincia_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->renderForm('provincia/' . $template, [
            'provincia' => $provincia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_provincia_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Provincia $provincia, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provincia->getId(), $request->request->get('_token'))) {
            $entityManager->remove($provincia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_provincia_index', [], Response::HTTP_SEE_OTHER);
    }
}
