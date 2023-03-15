<?php

namespace App\Controller;

use App\Reports\BasePdf;
use App\Repository\ColegioRepository;
use App\Repository\CircunscripcionRepository;
use App\Repository\ProfesionalRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;

 /**
 * ReportProfesionalesModificadosController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
class ReportProfesionalesModificadosController extends AbstractController
{
    #[Route('/reporteModifIndex', name: 'report_modificaciones_index')]
    public function index(ColegioRepository $ColegioRepository, CircunscripcionRepository $CircunscripcionRepository): Response
    {
        $colegios = $ColegioRepository->findAllArray();
        $circunscripciones = $CircunscripcionRepository->findAllArray();
         $hoy = date("Y-m-d");
       
       
        return $this->render('reports/reportProfesionalesModificadosIndex.html.twig', [
            'controller_name' => 'ReportProfesionalesModificadosController',
            'colegios' => $colegios,
            'circunscripciones' => $circunscripciones,
            'hoy' => $hoy
        ]);
    }

    /**
     * Reportes de Modificaciones
     * @Route("/ReporteModifGenerar", name="report_modificaciones_generar", defaults={"fechaModif" = 0}, requirements={"fechaModif"="\d+"})
     */
    public function generarReporte(
        Request $request,
        ProfesionalRepository $profesionalRepository,
        ColegioRepository $colegioRepository,
        CircunscripcionRepository $circunscripcionRepository,
        BasePdf $pdf,
    ): Response
    {

      //recupero datos seleccionados en la primer pantalla
      $idCircunscripcion = $request->request->get('circunscripcion');
      $idColegio = $request->request->get('colegio');
      $fechaDesde = $request->request->get('fDesde');
      $fechaHoraDesde = date($fechaDesde . ' 00:00:00');
      $fechaHasta = $request->request->get('fHasta');
      $fechaHoraHasta = date($fechaHasta . ' 23:59:59');
      
      if($idCircunscripcion<>9999 && $idColegio<>9999){
        $profesionales = $profesionalRepository->findByColegCircRangoFechaModificacion($idColegio, $idCircunscripcion, $fechaHoraDesde, $fechaHoraHasta);
        $colegio = $colegioRepository->findById($idColegio)->getColegio();
        $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion();
      }
        else{
          if($idCircunscripcion==9999 && $idColegio==9999){
            $profesionales = $profesionalRepository->findByRangoFechaModificacion($fechaHoraDesde, $fechaHoraHasta);
            $colegio = 'Todos';
            $circunscripcion = 'Todas';
          }
          else{
            if($idCircunscripcion==9999 && $idColegio<>9999){
              $profesionales = $profesionalRepository->findByColegRangoFechaModificacion($idColegio, $fechaHoraDesde, $fechaHoraHasta);
              $colegio = $colegioRepository->findById($idColegio)->getColegio();
              $circunscripcion =  'Todas';
            }
            else{
              if($idCircunscripcion<>9999 && $idColegio==9999){
                $profesionales = $profesionalRepository->findByCircRangoFechaModificacion($idCircunscripcion, $fechaHoraDesde, $fechaHoraHasta);
                $colegio =  'Todos';
                $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion();
              }
            }
          }
        }

        $html = $this->renderView('reports/reportProfesionalesModificadosPdf.html.twig', array(
            'profesionales' => $profesionales,
            'labelTitulo' => 'Listado de Profesionales Modificados',
            'labelColegio' => $colegio,
            'labelCircu' => $circunscripcion,
            'labelFechaDesde' => $fechaDesde,
            'labelFechaHasta' => $fechaHasta

        ));
        

        $nombreReporte = 'ListadoDeProfesionalesModificados.pdf';

       // return new Response($html);
        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
