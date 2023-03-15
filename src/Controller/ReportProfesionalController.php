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
 * ReportProfesionalController
 * 
 * @author Mercedes Valoni <mvaloni@justiciasantafe.gov.ar>
 * 
 */
#[Route('/reporte/profesional')]
class ReportProfesionalController extends AbstractController
{
    #[Route('/', name: 'report_profesional_index')]
    public function index(ColegioRepository $colegioRepository, CircunscripcionRepository $circunscripcionRepository): Response
    {
        $circunscripciones = $circunscripcionRepository->findAllArray();
        $colegios = $colegioRepository->findAllArray();
        return $this->render('reports/reportProfesionalIndex.html.twig', ['circunscripciones' => $circunscripciones, 'colegios' => $colegios]);
    }


    #[Route('/generar', name: 'report_profesional_generar')]
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
        $matricula = $request->request->get('matricula');
        
        $profesional = $profesionalRepository->findByMatriculaColegioCircunscripcion($idColegio, $idCircunscripcion, $matricula);

        $colegio = $colegioRepository->findById($idColegio) ? $colegioRepository->findById($idColegio)->getColegio() : 'TODOS';
        $circunscripcion = $circunscripcionRepository->findById($idCircunscripcion)? $circunscripcionRepository->findById($idCircunscripcion)->getCircunscripcion() : 'TODAS';

        $html = $this->renderView('reports/reportProfesionalPdf.html.twig', array(
            'profesional' => $profesional,
            'labelTitulo' => 'Datos del Profesional Matricula: ' . $matricula,
            'labelSubtitutlo' => 'Colegio: ' . $colegio,
            'labelSubsubtitulo' => 'Circunscripción: ' . $circunscripcion
        ));
        //return new Response($html);

        $nombreReporte = 'ListadoDatosProfesional.pdf';

        return new PdfResponse($pdf->getPdf()->getOutputFromHtml($html), $nombreReporte);
    }

}
