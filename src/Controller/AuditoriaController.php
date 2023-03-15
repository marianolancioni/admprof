<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AuditService;

use Omines\DataTablesBundle\DataTableFactory;
use App\DataTables\AuditoriaTableType;

 /**
 * AuditoriaController
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class AuditoriaController extends AbstractController
{
    protected $_auditService;

    /**
     * Variable auxiliar para crear datatables
     *
     * @var [DataTableFactory]
     */
    protected $datatableFactory;


    public function __construct(DataTableFactory $datatableFactory, AuditService $auditService)
    {
        $this->datatableFactory = $datatableFactory;
        $this->_auditService = $auditService;
    }
    
    #[Route('/auditoria', name: 'app_auditoria_index')]
    public function index(Request $request): Response
    {

        $auditoriaDataConfig = $this->_auditService->getConfig();

        $table = $this->datatableFactory->createFromType(AuditoriaTableType::class, array($auditoriaDataConfig))->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('auditoria/index.html.twig', ['datatable' => $table]);        

    }

    /**
     * @Route("/{tableName}/activate", name="auditoria_activate", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function activate(Request $request, String $tableName): Response
    {
        if ($this->isCsrfTokenValid('activate' . $tableName, $request->request->get('_token'))) {
            $message = $this->_auditService->activate($tableName);

            if (!$message) {
                $this->addFlash('success', 'Se ha activado la Auditoría para la entidad <b>"' . $tableName . '"</b>' . $message);
            }
            else {
                $this->addFlash('danger-slow', 'Ha ocurrido un inconveniente al intentar activar la Auditoría para la entidad <b>"' . $tableName . '"</b>.<hr>' . $message);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }    

    /**
     * @Route("/{tableName}/pause", name="auditoria_pause", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function pause(Request $request, String $tableName): Response
    {
        if ($this->isCsrfTokenValid('pause' . $tableName, $request->request->get('_token'))) {
            $message = $this->_auditService->pause($tableName);

            if (!$message) {
                $this->addFlash('success', 'Se ha detenido la Auditoría para la entidad <b>"' . $tableName . '"</b>' . $message);
            }
            else {
                $this->addFlash('danger-slow', 'Ha ocurrido un inconveniente al intentar pausar la Auditoría para la entidad <b>"' . $tableName . '"</b>.<hr>' . $message);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }      

    /**
     * @Route("/{tableName}/resume", name="auditoria_resume", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function resume(Request $request, String $tableName): Response
    {
        if ($this->isCsrfTokenValid('resume' . $tableName, $request->request->get('_token'))) {
            $message = $this->_auditService->resume($tableName);

            if (!$message) {
                $this->addFlash('success', 'Se ha reanudado la Auditoría para la entidad <b>"' . $tableName . '"</b>' . $message);
            }
            else {
                $this->addFlash('danger-slow', 'Ha ocurrido un inconveniente al intentar pausar la Auditoría para la entidad <b>"' . $tableName . '"</b>.<hr>' . $message);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }      

    /**
     * @Route("/{tableName}/delete", name="auditoria_delete", methods={"POST"})
     * 
     */
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, String $tableName): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tableName, $request->request->get('_token'))) {
            $message = $this->_auditService->delete($tableName);

            if (!$message) {
                $this->addFlash('success', 'Se han eliminado los registros de Auditoría para la entidad <b>"' . $tableName . '"</b>' . $message);
            }
            else {
                $this->addFlash('danger-slow-closable', 'Ha ocurrido un inconveniente al intentar eliminar los registros de Auditoría para la entidad <b>"' . $tableName . '"</b>.<hr>' . $message);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }

        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }      
    
}
