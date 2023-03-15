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
 * ReportColegiosModificadosController
 * 
 * @author Gustavo Adolfo Muller <gmuller@justiciasantafe.gov.ar>
 * 
 */
class ReportColegiosModificadosController extends AbstractController
{
    #[Route('/reporteColeModifIndex', name: 'report_colemodificaciones_index')]
    public function index(ColegioRepository $ColegioRepository, CircunscripcionRepository $CircunscripcionRepository): Response
    {

        $hoy = date("Y-m-d");         
        return $this->render('reports/reportColegiosModificadosIndex.html.twig', [
            'controller_name' => 'ReportColegiosModificadosController',
            'hoy' => $hoy
        ]);
    }

    /**
     * Reportes de Modificaciones
     * @Route("/ReporteColeModifGenerar", name="report_colemodificaciones_generar", defaults={"fechaModif" = 0}, requirements={"fechaModif"="\d+"})
     */
    public function generarReporte(
        Request $request,
        ProfesionalRepository $profesionalRepository,
        ColegioRepository $colegioRepository,
        CircunscripcionRepository $circunscripcionRepository,
        BasePdf $pdf,
    ): Response
    {

      $fechaDesde = $request->request->get('fDesde');
      $fechaHoraDesde = date($fechaDesde . ' 00:00:00');
      $fechaHasta = $request->request->get('fHasta');
      $fechaHoraHasta = date($fechaHasta . ' 23:59:59');
      
      //inicializa vector colemodi
      $colegios = $colegioRepository->findAllArray();//trae en orden   
      $circunscripciones = $circunscripcionRepository->findAllArray();

      $arreglo = [];
      $cantidad = 0;
      foreach($colegios as $colegio){ //para cada colegio
        foreach($circunscripciones as $circunscripcion){ //para cada circunscripcion

        //  $profesionales = $profesionalRepository->findByColegCircFechaModificacion($colegio["id"],$circunscripcion["id"],$fechaHoraDesde,$fechaHoraHasta);
        //  $cantidadmod = 0;
        //  foreach($profesionales as $profesional){
        //    $cantidadmod += 1;
        //  }
          $cantidadmod = 0;
          $cantidadmod  =  $profesionalRepository->findByCantiMod($colegio["id"],$circunscripcion["id"],$fechaHoraDesde,$fechaHoraHasta);

         // $profesionales2 = $profesionalRepository->findByColegCircFechaAsignacion($colegio["id"],$circunscripcion["id"],$fechaHoraDesde,$fechaHoraHasta);
        //  $cantidadasi = 0;
        //  foreach($profesionales2 as $profesional2){
        //    $cantidadasi += 1;
        //  }
          $cantidadasi = 0;
          $cantidadasi =$profesionalRepository->findByCantiAsigna($colegio["id"],$circunscripcion["id"],$fechaHoraDesde,$fechaHoraHasta);
          

          //verifica canti mod claves  
          if ($cantidadmod <> 0 || $cantidadasi <> 0){
            $cantidad += 1;        
            $arreglo[$cantidad][0] = $colegio["colegio"];//$colegio;//descripcion colegio
            $arreglo[$cantidad][1] = $circunscripcion["circunscripcion"];//$circunscripcion;// descr circunscripcion
            $arreglo[$cantidad][2] = $cantidadmod; //cantidad modificados prof
            $arreglo[$cantidad][3] = $cantidadasi; //cantidad modifi claves      
          }             
        }// circunscripciones
      }//colegios
       
        $html = $this->renderView('reports/reportColegiosModificadosPdf.html.twig', array(            
            'labelTitulo' => 'Resumen de Modificaciones y Asignaciones por Colegios',//Listado de Resumen de Modificaciones y Asignaciones por Colegios
            'labelFechaDesde' => $fechaDesde,
            'registros' => $arreglo,
            'cantregistros' => $cantidad,
            'labelFechaHasta' => $fechaHasta
        ));        

        $nombreReporte = 'ListadoDeColegiosModificados.pdf';
        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
