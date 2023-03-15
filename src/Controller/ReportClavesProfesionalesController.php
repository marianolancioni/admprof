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

use App\Service\ProfesionalService;

 /**
 * ReportClavesProfesionalesController
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class ReportClavesProfesionalesController extends AbstractController
{
    #[Route('/reporteClavesIndex', name: 'report_claves_profesionales_index')]
    public function index(ColegioRepository $ColegioRepository, CircunscripcionRepository $CircunscripcionRepository): Response
    {
        $colegios = $ColegioRepository->findAllArray();
        $circunscripciones = $CircunscripcionRepository->findAllArray();
         $hoy = date("Y-m-d");
       
       
        return $this->render('reports/reportClavesProfesionalesIndex.html.twig', [
            'controller_name' => 'ReportClavesProfesionalesController',
            'colegios' => $colegios,
            'circunscripciones' => $circunscripciones,
            'hoy' => $hoy
        ]);
    }

    /**
     * Reportes de Claves
     * @Route("/ReporteClavesfGenerar", name="report_claves_generar", defaults={"fechaModif" = 0}, requirements={"fechaModif"="\d+"})
     */
    public function generarReporte(
        Request $request,
        ProfesionalRepository $profesionalRepository,
        ColegioRepository $colegioRepository,
        CircunscripcionRepository $circunscripcionRepository,
        BasePdf $pdf,
        ProfesionalService $ProfesionalService,
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

        $html = $this->renderView('reports/reportClavesProfesionalesPdf.html.twig', array(
            'profesionales' => $profesionales,
            'labelTitulo' => 'Listado de Claves de Profesionales',
            'labelColegio' => $colegio,
            'labelCircu' => $circunscripcion,
            'labelFechaDesde' => $fechaDesde,
            'labelFechaHasta' => $fechaHasta,
            'profesionalService' => $ProfesionalService,

        ));
        

        $nombreReporte = 'ListadoDeProfesionalesModificados.pdf';

        //$pdf->setOption('default-header', 'asdf');

       // return new Response($html);
        return new PdfResponse(
          $pdf->getPdf()
              ->setOption('header-right', '')
              ->setOption('footer-left', '')
              ->setOption('header-html', '')
              ->setOption('header-line', false)
              ->setOption('footer-line', false)
              ->setOption('margin-top', 10)
              ->getOutputFromHtml($html), $nombreReporte);
    }

}
