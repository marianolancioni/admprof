<?php

namespace App\Controller;

use App\Entity\Profesional;
use App\Form\ProfesionalType;
use App\Repository\CircunscripcionRepository;
use App\Repository\ColegioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; 
use Omines\DataTablesBundle\DataTableFactory; 
use App\DataTables\ProfesionalTableType;
use App\Service\ProfesionalService;
use App\Service\UsuarioLogAccionService;

 /**
 * ProfesionalController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
#[Route('/profesional')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ProfesionalController extends AbstractController
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


    
    /**
     * @Route("/{filtroEstadoProfesional}/{filtroBajaProfesional}/{filtroCircunscripcion}/{filtroColegio}", name="app_profesional_index", defaults={"filtroEstadoProfesional" = 0, "filtroBajaProfesional" = 2, "filtroCircunscripcion" = 0, "filtroColegio" = 99}, requirements={"filtroEstadoProfesional"="\d+" , "filtroBajaProfesional"="\d+", "filtroCircunscripcion"="\d+", "filtroColegio"="\d+"}, options={"expose"=true})
     */
     
    
    public function index(Request $request, CircunscripcionRepository $circunscripcionRepository, ColegioRepository $colegioRepository, int $filtroEstadoProfesional, int $filtroBajaProfesional, int $filtroCircunscripcion, int $filtroColegio): Response
    {  
        if (!$this->getUser()->getCircunscripcion()) {
            $circunscripcionesList = $circunscripcionRepository->findAllCircunscripciones();
        }

        if (!$this->getUser()->getColegio()) {
            $colegiosList = $colegioRepository->findAllColegios();
        }

        $table = $this->datatableFactory->createFromType(ProfesionalTableType::class, array($filtroEstadoProfesional, $filtroBajaProfesional, $filtroCircunscripcion, $filtroColegio))->handleRequest($request);    
        if ($table->isCallback()) 
        {            
            return $table->getResponse();        
        }
        return $this->render('profesional/index.html.twig', ['datatable' => $table, 
                'filtroEstadoProfesional' => $filtroEstadoProfesional, 
                'filtroBajaProfesional' => $filtroBajaProfesional,
                'circunscripcionesList' => $circunscripcionesList ?? null,
                'filtroCircunscripcion' => $filtroCircunscripcion ?? 0,
                'colegiosList'          => $colegiosList ?? null,
                'filtroColegio'         => $filtroColegio ?? 99,
            ]); 
    }

    #[Route('/new', name: 'app_profesional_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_EDITOR")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profesional = new Profesional();
        $colUsuario = $this->getUser()->getColegio();
        $circUsuario = $this->getUser()->getCircunscripcion();
        $validacion = 'new';
        $profesional->setFechaActualizacion(new \DateTime());
        $profesional->setFechaAlta(new \DateTime());
        $profesional->setColegio($colUsuario);
        $profesional->setCircunscripcion($circUsuario);
        $form = $this->createForm(ProfesionalType::class, $profesional, array('cole' => $colUsuario,'circu' => $circUsuario, 'validacion' => $validacion));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $profesional->setDesde(new \DateTime());
            $profesional->setEstado(0);
            $entityManager->persist($profesional);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha creado el profesional: ' . $profesional->getApellido() . ', ' . $profesional->getNombre()); 
             if ($request->isXmlHttpRequest()) 
             { 
                     return new Response(null, 204); 
            } 

            return $this->redirectToRoute('app_profesional_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig'; 
        return $this->renderForm('profesional/' . $template, [
            'profesional' => $profesional,
            'form' => $form,
        ], new Response( 
                    null, 
                   $form->isSubmitted() && !$form->isValid() ? 422 : 200, 
                 )); 
     }


    #[Route('/{id}/show', name: 'app_profesional_show', methods: ['GET'])]
    public function show(Profesional $profesional): Response
    {
        return $this->render('profesional/show.html.twig', [
            'profesional' => $profesional,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profesional_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_EDITOR")]
    public function edit(Request $request, Profesional $profesional, EntityManagerInterface $entityManager): Response
    {
        $colUsuario = $this->getUser()->getColegio();
        $circUsuario = $this->getUser()->getCircunscripcion();
        $validacion = 'edit';
       $form = $this->createForm(ProfesionalType::class, $profesional, array('cole' => $colUsuario,'circu' => $circUsuario, 'validacion' => $validacion)); 
       $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profesional->setFechaActualizacion(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Se ha modificado el profesional: ' . $profesional->getApellido() . ', ' . $profesional->getNombre()); 
             if ($request->isXmlHttpRequest()) 
             { 
                     return new Response(null, 204); 
            } 

            return $this->redirectToRoute('app_profesional_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig'; 
        return $this->renderForm('profesional/' . $template, [
            'profesional' => $profesional,
            'form' => $form,
        ], new Response( 
                    null, 
                    $form->isSubmitted() && !$form->isValid() ? 422 : 200, 
                 )); 
         }

    
    
    #[Route('/{id}/delete', name: 'app_profesional_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Profesional $profesional, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profesional->getId(), $request->request->get('_token'))) {
            //No borro definitvamente el profesional sino que le seteo una fecha de baja (baja lógica) y estado = 1.
            $profesional->setFechaActualizacion(new \DateTime());
            $profesional->setFechaBaja(new \DateTime('now'));
            $profesional->setEstado(1);
            $entityManager->flush();

            $this->addFlash('danger', 'Se ha dado de baja al profesinal: ' . $profesional->getApellido() . ', ' . $profesional->getNombre());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('app_profesional_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/undelete", name="profesional_undelete", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function undelete(Request $request, Profesional $profesional, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('undelete' . $profesional->getId(), $request->request->get('_token'))) {
            $profesional->setFechaActualizacion(new \DateTime());
            $profesional->setFechaBaja(null);
            $profesional->setEstado(0);
            $entityManager->flush();

            $this->addFlash('success', 'Se ha reactivado al profesional: ' . $profesional->getApellido() . ', ' . $profesional->getNombre());

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('app_profesional_index', [], Response::HTTP_SEE_OTHER);
    }


    //ASIGNA CLAVE A PROFESIONAL
    #[Route('/{id}/asignaclave', name: 'asigna_clave_profesional', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_EDITOR")]
    public function asignaClaveProfesional(Request $request, Profesional $profesional,ProfesionalService $ProfesionalService, EntityManagerInterface $entityManager): Response
    {
        $claveAleatoria = $ProfesionalService->GeneraClaveNum4();
        $claveEncriptada = $ProfesionalService->encrypt_decrypt('encrypt', $claveAleatoria);
        $profesional->setClave($claveEncriptada);
        $profesional->setFechaClave(new \DateTime());
        $entityManager->persist($profesional);
        $entityManager->flush();

        $this->addFlash('success-slow', 'Se ha generado nueva clave para el profesional: <b>' . $profesional->getApellido() . ', ' . $profesional->getNombre() . '</b><br>NUEVA CLAVE: <strong>' . $claveAleatoria . '</strong>');

        if ($request->isXmlHttpRequest()) {
            return new Response(null, 204);
        }

        return $this->redirectToRoute('app_profesional_index', [], Response::HTTP_SEE_OTHER);

    }

    //MUESTRA CLAVE DEL PROFESIONAL
    #[Route('/{id}/showClave', name: 'app_profesional_showClave', methods: ['GET'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function showClave(Profesional $profesional, ProfesionalService $ProfesionalService): Response
    {
        $claveEncrip = $profesional->getClave();
        return $this->render('profesional/showClave.html.twig', [
            'profesionalService' => $ProfesionalService,
            'claveEncrip' => $claveEncrip,
        ]);
    }

    /**
     * Importa desde IOL Profesionales de de un colegio y circunscripcion dados. Admite importación completa sin Colegio ni Circunscripción.
     * 
     * Al procesar, se borran todos los registros de la entidad de Profesional y se insertan a partir de los existentes en IOL
     */
    #[Route('/import', name: 'app_profesional_import', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function import(ProfesionalService $profesionalService, UsuarioLogAccionService $usuarioLogAccionService, Request $request): Response
    {
        $colegios = $profesionalService->traeColegios();//carga  array colegios que recorre y carga en el template
        $circunscripciones = $profesionalService->traeCircunscripciones();//carga  array circunscripcion que recorre y carga en el template

        if ($request->request->get('circunscripcion') !== null && $request->request->get('colegio') !== null) {
            $result = $profesionalService->importFromIol( $request->request->get('circunscripcion'), $request->request->get('colegio') );
            if ($result[0] == -1) {
                $usuarioLogAccionService->logAction($request->getClientIp(), 'Importación de Prof. desde IOL (Circ. ' . $request->request->get('circunscripcion') . ', ID Colegio ' . $request->request->get('colegio') . ') - ' . $result[1] . ' importados. IMPORTACION ABORTADA!');
                return new Response('<b>Se produjo un error mientras se procesaba la importación.<br><br>Antes del mismo se alcanzaron a procesar satisfactoriamente ' . $result[1] . ' profesionales.</b><br><br>Detalles del error:<hr>' . $result[2], 500); 
            }
            else {
                $procesados = $result[0];
                $usuarioLogAccionService->logAction($request->getClientIp(), 'Importación de Prof. desde IOL (Circ. ' . $request->request->get('circunscripcion') . ', ID Colegio ' . $request->request->get('colegio') . ') - ' . $procesados . ' importados');
                $this->addFlash('success-slow', 'La importación se ha realizado satisfactoriamente.<br>Se importaron <strong>' . $procesados . '</strong> Profesionales.');
            }
        }

        return $this->render('profesional/import.html.twig', [
            'colegios' => $colegios,
            'circunscripciones' => $circunscripciones,
            'colegioId' => $request->request->get('colegio') ?? 99, 
            'circunscripcionId' => $request->request->get('circunscripcion') ?? 0,
            'procesados' => $procesados ?? null,
        ]);
    }
         
}
