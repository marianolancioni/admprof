<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\ProfesionalService;
use App\Service\ExportadoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;// no quitar, da error

#[IsGranted("IS_AUTHENTICATED_FULLY")]
/**
 * ExportadoController
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 * 
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ExportadoController extends AbstractController
{
    #[Route('/exportado', name: 'app_exportado')]
    public function index(ProfesionalService $ProfesionalService): Response
    {
        $colegios = $ProfesionalService->traeColegios();//carga  array colegios que recorre y carga en el template
        $circunscripciones = $ProfesionalService->traeCircunscripciones();//carga  array circunscripcion que recorre y carga en el template
        return $this->render('exportado/index.html.twig', [
            'controller_name' => 'ExportadoController',
            'colegios' => $colegios,
            'circunscripciones' => $circunscripciones
        ]);
    }

    #[Route('/exportadogenera', name: 'exportado_genera' )]
    public function exportadoGenera(Request $request,  ProfesionalService $ProfesionalService, ExportadoService $ExportadoService): Response
    {
        //recupero datos seleccionados en la primer pantalla
            $circunscripcionID = $request->request->get('circunscripcion');
            $colegioID = $request->request->get('colegio');
            $dbf = $request->request->get('cbidbf');//check box dbf
            $claves = $request->request->get('cbclave');//check box CLAVES
            $fechaDesde = $request->request->get('fDesde');//string  2022-04-08
         //recupero datos seleccionados en la primer pantalla

        $exporta = '';//variable para mostrar la cantidfad de exportados y mensajes
        $colegioDesc = $ProfesionalService->traeColegio($colegioID);//descripcion del colegio seleccionado
        $circunscripcionDesc = $ProfesionalService->traeCircunscripcion($circunscripcionID);//descripcion de la circunscripcion  seleccionada     

        //exporta para el cole y circ en csv o dbf considerando modificaicones desde fecha si no esta vacia y devuelve la cantidad de registros exportados
        //deja en el servidor el archivo en carpeta resources/exportados  lo puse fijo para sacarlo del public y que no se acceda por url
        $exporta = $ExportadoService->exportaCsv( $colegioID, $circunscripcionID, $dbf, $fechaDesde,$claves);

        //muestra la segunda pantalla con mensaje de cantidad expoortada y opcion de descargar el archivo generado.      
       return $this->render('exportado/resultado.html.twig', [
        'controller_name' => 'ExportadoController',
        'exporta' => $exporta, 
        'colegio' => $colegioID,
        'circunscripcion' => $circunscripcionID,
        'colegioDesc' => $colegioDesc,
        'circunscripcionDesc' => $circunscripcionDesc ,  
        'esdbf'      => $dbf, 
        'fdesde' => $fechaDesde,  
        'esclave'      => $claves 
       ]);

    }

    //descarga el archivo csv o pdf a la pc del usuario
    #[Route('/exportadodescarga', name: 'exportado_descarga' )]
    public function exportadoDescarga(Request $request): Response
    {
        //datos de la pantalla para saber que archivo se genero en el servidor
        $circunscripcionID = $request->request->get('circunscripcion');
        $colegioID = $request->request->get('colegio');
        $dbf = $request->request->get('generodbf');
        $desdeh = $request->request->get('fdesdeh');
        $claves  = $request->request->get('generoclave');
        //datos de la pantalla para saber que archivo se genero en el servidor

        $current_dir_path = getcwd(); //se descartó para que no este accesible por url
      //  $current_dir_path = '../resources';
        $new_dir_path = $current_dir_path . "/exportados";        
      

        if ($dbf){
            if ($claves){
                $archivo = $new_dir_path . "/colpassw" . strval($colegioID) . '_' . strval($circunscripcionID) . ".dbf";
                $name= "colpassw" . strval($colegioID) . '_' . strval($circunscripcionID) . ".dbf";

                if (file_exists($archivo)) {
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename=$name");
                    header("Content-Type: application/dbf");
                    header("Content-Transfer-Encoding: binary");
                    header('Content-Length: ' . filesize($archivo));
                   // readfile($archivo);
                    return new Response(file_get_contents($archivo), 200, array('Content-Type' => 'application/dbf'));
                }
            }else{
                $archivo = $new_dir_path . "/colegio" . strval($colegioID) . '_' . strval($circunscripcionID) . ".dbf";
                $name= "colegio" . strval($colegioID) . '_' . strval($circunscripcionID) . ".dbf";

                if (file_exists($archivo)) {
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename=$name");
                    header("Content-Type: application/dbf");
                    header("Content-Transfer-Encoding: binary");  
                    header('Content-Length: ' . filesize($archivo)); 
                  // readfile($archivo);
                  return new Response(file_get_contents($archivo), 200, array('Content-Type' => 'application/dbf'));
                }
            }
        } else        {// si es csv
            if ($claves){
                $archivo = $new_dir_path . "/colpassw" . strval($colegioID) . '_' . strval($circunscripcionID) . ".csv";
                $name= "colpassw" . strval($colegioID) . '_' . strval($circunscripcionID) . ".csv";
                if (file_exists($archivo)) {
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename=$name");
                    header("Content-Type: application/csv");
                    header("Content-Transfer-Encoding: binary");
                    header('Content-Length: ' . filesize($archivo));
                    //readfile($archivo); 
                    return new Response(file_get_contents($archivo), 200, array('Content-Type' => 'application/csv'));
                }              
            }else{    
                $archivo = $new_dir_path . "/colegio" . strval($colegioID) . '_' . strval($circunscripcionID) . ".csv";
                $name= "colegio" . strval($colegioID) . '_' . strval($circunscripcionID) . ".csv";
                if (file_exists($archivo)) {
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename=$name");
                    header("Content-Type: application/csv");
                    header("Content-Transfer-Encoding: binary");
                    header('Content-Length: ' . filesize($archivo));
                   // readfile($archivo);
                   return new Response(file_get_contents($archivo), 200, array('Content-Type' => 'application/csv'));
                }    
            }
        }
     //   return new Response(file_get_contents($archivo), 200, array('Content-Type' => 'application/csv')); otra opción a analizar que usó martin en otro sistema symfony
    }
}
