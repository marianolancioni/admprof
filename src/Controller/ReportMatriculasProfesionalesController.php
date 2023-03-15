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
 * ReportMatriculasProfesionalesController
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 */
#[Route('/reporte/matricula/profesionales')]
class ReportMatriculasProfesionalesController extends AbstractController
{
    #[Route('/', name: 'report_matriculas_profesionales_index')]
    public function index(ColegioRepository $colegioRepository, CircunscripcionRepository $circunscripcionRepository): Response
    {
        $circunscripciones = $circunscripcionRepository->findAllArray();
        $colegios = $colegioRepository->findAllArray();

        return $this->render('reports/reportMatriculasProfesionalesIndex.html.twig', ['circunscripciones' => $circunscripciones, 'colegios' => $colegios]);
    }


    #[Route('/generar', name: 'report_matriculas_profesionales_generar')]
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
        
        $profesionales = $profesionalRepository->findByColegioCircunscripcion($idColegio, $idCircunscripcion);

        $colegio = $colegioRepository->findById($idColegio) ? $colegioRepository->findById($idColegio)->getColegio() : 'TODOS';
        $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)? $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion() : 'TODAS';
        
        $cantidad = count($profesionales);      

        $html = $this->renderView('reports/reportMatriculasProfesionalesPdf.html.twig', array(
            'profesionales' => $profesionales,
            'labelTitulo' => 'Listado de Profesionales',
            'labelSubtitutlo' => 'COLEGIO: ' . $colegio,
            'labelSubsubtitulo' => 'CIRCUNSCRIPCION: ' . $circunscripcion,
            'cantidadTotal'  => $cantidad
        ));
        //return new Response($html);

        $nombreReporte = 'ListadoDeProfesionales.pdf';

        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
