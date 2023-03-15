<?php

namespace App\Controller;

use App\Entity\TipoDocumento;
use App\Form\TipoDocumentoType;
use App\Repository\TipoDocumentoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; 
use Omines\DataTablesBundle\DataTableFactory; 
use App\DataTables\TipoDocumentoTableType;

 /**
 * TipoDocumentoController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
#[Route('/tipo/documento')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[IsGranted("ROLE_ADMIN")]
class TipoDocumentoController extends AbstractController
{
      /**     
       * Variable auxiliar para crear datatables     
       * 
       * @var [DataTableFactory]     
       **/   
      
       protected $datatableFactory;

       public function __construct(DataTableFactory $datatableFactory)    
       {        
           $this->datatableFactory = $datatableFactory;    
        }


    #[Route('/', name: 'app_tipo_documento_index')]
    public function index(Request $request): Response 
    {
        
        $table = $this->datatableFactory->createFromType(TipoDocumentoTableType::class, array())->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('tipo_documento/index.html.twig', ['datatable' => $table]);
    }

    #[Route('/new', name: 'app_tipo_documento_new', methods: ['GET', 'POST'])]
   // public function new(Request $request, TipoDocumentoRepository $tipoDocumentoRepository): Response
   #[IsGranted("ROLE_SUPER_ADMIN")]
   public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoDocumento = new TipoDocumento();
        $form = $this->createForm(TipoDocumentoType::class, $tipoDocumento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$tipoDocumentoRepository->add($tipoDocumento);
           // return $this->redirectToRoute('app_tipo_documento_index', [], Response::HTTP_SEE_OTHER);
           $entityManager ->persist($tipoDocumento);
           $entityManager ->flush();  

           $this->addFlash('success', 'Se ha creado el tipo de documento: <b>' . $tipoDocumento->getTipoDocumento() . '</b>');
           if ($request->isXmlHttpRequest()) {
            return new Response(null, 204);
         }
         return $this->redirectToRoute('tipo_documento_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('tipo_documento/' . $template, [
            'tipo_documento' => $tipoDocumento,
            'form' => $form,
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
         ));
    }

    #[Route('/{id}', name: 'app_tipo_documento_show', methods: ['GET'])]
    public function show(TipoDocumento $tipoDocumento): Response
    {
        return $this->render('tipo_documento/show.html.twig', [
            'tipo_documento' => $tipoDocumento,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tipo_documento_edit', methods: ['GET', 'POST'])]
    //public function edit(Request $request, TipoDocumento $tipoDocumento, TipoDocumentoRepository $tipoDocumentoRepository): Response
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, TipoDocumento $tipoDocumento, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TipoDocumentoType::class, $tipoDocumento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado el tipo de documento: <b>' . $tipoDocumento->getTipoDocumento() . '</b>');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
        }

        return $this->redirectToRoute('app_tipo_documento_index', [], Response::HTTP_SEE_OTHER);
    }

    $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
    return $this->renderForm('tipo_documento/' . $template, [
        'tipoDocumento' => $tipoDocumento,
        'form' => $form,
    ]);
    }

    #[Route('/{id}', name: 'app_tipo_documento_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, TipoDocumento $tipoDocumento, TipoDocumentoRepository $tipoDocumentoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoDocumento->getId(), $request->request->get('_token'))) {
            $tipoDocumentoRepository->remove($tipoDocumento);
        }

        return $this->redirectToRoute('app_tipo_documento_index', [], Response::HTTP_SEE_OTHER);
    }
}
