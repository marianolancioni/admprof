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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

 /**
 * ReportAsignacionClaveController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * 
 */
class ReportAsignacionClaveController extends AbstractController
{
    #[Route('/reporteIndex', name: 'report_asignacionclave_index')]
    public function index(ColegioRepository $ColegioRepository, CircunscripcionRepository $CircunscripcionRepository): Response
    {
        $colegios = $ColegioRepository->findAllArray();
        $circunscripciones = $CircunscripcionRepository->findAllArray();
         $hoy = date("Y-m-d");
       
       
        return $this->render('reports/reportAsignacionClaveIndex.html.twig', [
            'controller_name' => 'ReportAsignacionClaveController',
            'colegios' => $colegios,
            'circunscripciones' => $circunscripciones,
            'hoy' => $hoy
        ]);
    }

    /**
     * Reportes de Asignación de claves
     * @Route("/ReporteGenerar", name="report_asignacionclave_generar", defaults={"fechaClave" = 0}, requirements={"fechaClave"="\d+"})
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
        $profesionales = $profesionalRepository->findByColegCircRangoFechaClave($idColegio, $idCircunscripcion, $fechaHoraDesde, $fechaHoraHasta);
        $colegio = $colegioRepository->findById($idColegio)->getColegio();
        $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion();
      }
        else{
          if($idCircunscripcion==9999 && $idColegio==9999){
            $profesionales = $profesionalRepository->findByRangoFechaClave($fechaHoraDesde, $fechaHoraHasta);
            $colegio = 'Todos';
            $circunscripcion = 'Todas';
          }
          else{
            if($idCircunscripcion==9999 && $idColegio<>9999){
              $profesionales = $profesionalRepository->findByColegRangoFechaClave($idColegio, $fechaHoraDesde, $fechaHoraHasta);
              $colegio = $colegioRepository->findById($idColegio)->getColegio();
              $circunscripcion =  'Todas';
            }
            else{
              if($idCircunscripcion<>9999 && $idColegio==9999){
                $profesionales = $profesionalRepository->findByCircRangoFechaClave($idCircunscripcion, $fechaHoraDesde, $fechaHoraHasta);
                $colegio =  'Todos';
                $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion();
              }
            }
          }
        }

        $html = $this->renderView('reports/reportAsignacionClavePdf.html.twig', array(
            'profesionales' => $profesionales,
            'labelTitulo' => 'Listado de Asignación de Claves',
            'labelColegio' => $colegio,
            'labelCircu' => $circunscripcion,
            'labelFechaDesde' => $fechaDesde,
            'labelFechaHasta' => $fechaHasta
        ));
        

        $nombreReporte = 'ListadoDeAsignacionClaves.pdf';

       // return new Response($html);
        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
