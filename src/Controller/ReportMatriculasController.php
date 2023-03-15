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
 * ReportMatriculasController
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 */
#[Route('/reporte/matricula')]
class ReportMatriculasController extends AbstractController
{
    #[Route('/', name: 'report_matriculas_index')]
    public function index(ColegioRepository $colegioRepository, CircunscripcionRepository $circunscripcionRepository): Response
    {
        // Propone fechas (dese el día actual hasta un 1 mes atrás adelante)
        $hoy = date("Y-m-d");
        $circunscripciones = $circunscripcionRepository->findAllArray();
        $colegios = $colegioRepository->findAllArray();

        return $this->render('reports/reportMatriculasIndex.html.twig', ['hoy' => $hoy, 'circunscripciones' => $circunscripciones, 'colegios' => $colegios]);
    }


    #[Route('/generar', name: 'report_matriculas_generar')]
    public function generar(
        Request $request, 
        ColegioRepository $colegioRepository, 
        CircunscripcionRepository $circunscripcionRepository, 
        ProfesionalRepository $profesionalRepository, 
        BasePdf $pdf
    ): Response
    {
        $idColegio = $request->request->get('colegio');
        $idCircunscripcion = $request->request->get('circunscripcion');
        $fechaDesde = $request->request->get('start');
        $fechaHasta = $request->request->get('end');
        
        $profesionales = $profesionalRepository->findByColegioCircunscripcionRangoAlta($idColegio, $idCircunscripcion, $fechaDesde, $fechaHasta);

        $colegio = $colegioRepository->findById($idColegio) ? $colegioRepository->findById($idColegio)->getColegio() : 'TODOS';
        $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)? $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion() : 'TODAS';

        $html = $this->renderView('reports/reportMatriculasPdf.html.twig', array(
            'profesionales' => $profesionales,
            'labelTitulo' => 'Listado de Matrículas por fecha de creación',
            'labelSubtitutlo' => 'COLEGIO: ' . $colegio,
            'labelSubsubtitulo' => 'CIRCUNSCRIPCION: ' . $circunscripcion,
            'labelFechaDesde' => $fechaDesde,
            'labelFechaHasta' => $fechaHasta
        ));
        //return new Response($html);

        $nombreReporte = 'ListadoDeMatriculas.pdf';

        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
