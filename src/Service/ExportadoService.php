<?php


namespace App\Service;

use App\Repository\ProfesionalRepository;
use App\Repository\ColegioRepository;
use App\Repository\CircunscripcionRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

/**
 * ExportadoService
 * 
 * Servicio utilizado para realizar validaciones y cálculos
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 */
class ExportadoService
{
    private $_profesionalRepository;
    private $_ColegioRepository;
    private $_CircunscripcionRepository;
    private $_mailer;

    public function __construct(ProfesionalRepository $profesionalRepository, ColegioRepository $ColegioRepository,  CircunscripcionRepository $CircunscripcionRepository, MailerInterface $mailer)
    {
        $this->_profesionalRepository =  $profesionalRepository;
        $this->_ColegioRepository =  $ColegioRepository;
        $this->_CircunscripcionRepository =  $CircunscripcionRepository;
        $this->_mailer = $mailer;
    }

    public function traeColegio($id) //con el id trae colegio para mostrar en combo//pantalla
    {
        if ($id <> 99) {
            $coreConfigItem = $this->_ColegioRepository->find($id); //recupera colegio
            return $coreConfigItem->getColegio();
        } else {
            return "TODOS";
        }
    }

    public function traeCircunscripcion($id) //con el id trae circunscripcion  para mostrar en combo//pantalla
    {
        if ($id <> 0) {
            $coreConfigItem = $this->_CircunscripcionRepository->find($id); //recupera circunscripcion
            return $coreConfigItem->getCircunscripcion();
        } else {
            return "TODAS";
        }
    }

    public function exportaCsv($cole, $circ, $dbf, $fechaDesde, $claves) //recibe colegio y circunscripción
    {
        $fechaDesdedate = date($fechaDesde);
        $coreConfigItem = $this->_profesionalRepository->findByColeCir($cole, $circ, $fechaDesdedate, $claves); //recupera instancia de objetos PROFESIONALES para cole circ y fecha         
        $cantidad = 0;
        if ($coreConfigItem) {
            $cantidad = $this->outputCSV($coreConfigItem, $cole, $circ, $dbf, $claves); //pone los profesionales en archivo dbf o csv
            $colegioDesc = $this->traeColegio($cole);
            $circunscripcionDesc = $this->traeCircunscripcion($circ);
            return 'Se exportaron ' . $cantidad . ' registros ' . ' para Colegio: ' . $colegioDesc . ' Circunscripción: ' .  $circunscripcionDesc;
        } else {
            return 'No se encontraron datos';
        }
    }

    public function outputCSV($data, $cole, $circ, $dbf, $claves, $useKeysForHeaderRow = true)
    {

        //require_once '../vendor/autoload.php'; //una opcion para manejo de archivos, necesario para que funcione Filesystem object (no se requiere)
        $fsObject = new Filesystem();
        $current_dir_path = getcwd(); //directorio actual que seria el public 
        // $current_dir_path = '../resources';

        //make a new directory si no existe creo el directorio
        try {
            $new_dir_path = $current_dir_path . "/exportados";

            if (!$fsObject->exists($new_dir_path)) {
                $fsObject->mkdir($new_dir_path, 0775); // 0775 ignorado en windows         
            }
        } catch (IOExceptionInterface $exception) {
            echo "Error creating directory at" . $exception->getPath();
        }

        $cantidad = 0; //cantidad de registros/profesionales exportados

        if ($dbf) { //si es dbf creo cabecera del archivo dbf
            if ($claves) {
                $archivo2 = $new_dir_path . "/colpassw" . strval($cole) . '_' . strval($circ) . ".dbf";     //para las claves 
                $def2 = array(
                    array("Ccirc",      "N",   2, 0),
                    array("Ccoleg",     "N",   2, 0),
                    array("Ctomo",      "C",   10),
                    array("Cpassw",   "C",   5),
                    array("Cfecasig",   "D"),
                    array("Chorasig",   "C",   5),
                    array("Csemilla",   "N",   9, 0)
                );
                //$db2 = dbase_create($archivo2, $def2);  //creo el archivo dbf colpassw
                if ($fsObject->exists('./exportados/colpassw.dbf')) { //si existe base lo tomo , funciona mejor con foxpro
                    copy('./exportados/colpassw.dbf', $archivo2);
                    $db2 = dbase_open($archivo2, 2);
                } else {
                    $db2 = dbase_create($archivo2, $def2);
                }
            } else {
                $archivo = $new_dir_path . "/colegio" . strval($cole) . '_' . strval($circ) . ".dbf";
                //encabezado o estructura  de la tabla dbf
                $def = array(
                    array("acirc",      "N",   2, 0),
                    array("acoleg",     "N",   2, 0),
                    array("atomo",      "C",   10),
                    array("anombre",    "C",   40),
                    array("adomici",    "C",   40),
                    array("alocali",    "C",   25),
                    array("aprovin",    "C",   15),
                    array("aestado",    "N",   2, 0),
                    array("afecdes",    "D"),
                    array("afechas",    "D"),
                    array("afecactm",   "D"),
                    array("ahoractm",   "C",   5)
                );
                //  $db = dbase_create($archivo, $def);  //creo el archivo dbf colegio   
                if ($fsObject->exists('./exportados/colegio.dbf')) {
                    copy('./exportados/colegio.dbf', $archivo);
                    $db = dbase_open($archivo, 2);
                } else {
                    $db = dbase_create($archivo, $def);
                }
            }
        } else {
            //crea archivo,
            //'w'	Apertura para sólo escritura; coloca el puntero al fichero al principio del fichero y trunca el fichero a longitud cero. 
            //Si el fichero no existe se intenta crear.
            if ($claves) {
                $outputBuffer2 = fopen($new_dir_path . "/colpassw" . strval($cole) . '_' . strval($circ) . ".csv", 'w');
            } else {
                $outputBuffer = fopen($new_dir_path . "/colegio" . strval($cole) . '_' . strval($circ) . ".csv", 'w');
            }
        }


        foreach ($data as $v) { //proceso profesionales(objetos) grabando en renglones del archivo correspondiente
            //tomo en cuenta el estado para exportar o no
            $estado = $v->getEstado();
            if ($estado == 0) {      //estado 0 es activo, 1 es baja logica (hay 1 en iol producción)      

                if ($claves) {
                    $cantidad += 1;
                    $arreglo[0] = $v->getCircunscripcion()->getId(); //ok
                    $arreglo[1] = $v->getColegio()->getId();  //ok
                    $arreglo[2] =  strtoupper($v->getMatricula()); // // ok
                    $AuxClave = $v->getClave();

                    if ($dbf) {
                        $AuxClave = mb_convert_encoding($AuxClave, "WINDOWS-1252", "UTF-8");
                    } else {
                        $AuxClave = mb_convert_encoding($AuxClave, "WINDOWS-1252", "UTF-8"); //hay que abrirlo con encoding windows, alguno1254.. "ISO-8859-2"
                    }

                    $arreglo[3] = substr($AuxClave, 0, 5);

                    if (is_null($v->getFechaClave())) {
                        $arreglo[4] = $v->getFechaClave();
                    } else {
                        if ($dbf) {
                            $arreglo[4] = $v->getFechaClave()->format('Ymd'); // date('Ymd');   //formato d efecha para dbf          
                        } else {
                            $arreglo[4] = $v->getFechaClave()->format('d/m/Y'); //formato d efecha para csv
                        }
                    }
                    if (is_null($v->getFechaClave())) {
                        $arreglo[5] = '00:00';
                    } else {
                        $arreglo[5] = $v->getFechaClave()->format('H:i'); //ver de donde sale, que no guardamos hora actualizacion...iol tampoco
                    }
                    $arreglo[6] = 0; //semilla ahora no guardamos

                    if ($dbf) { //agrego el renglon en el dbf o csv
                        dbase_add_record($db2, $arreglo);
                    } else {
                        fputcsv($outputBuffer2, $arreglo);
                    }
                } else {
                    $cantidad += 1;
                    $arreglo[0] = $v->getCircunscripcion()->getId(); //ok
                    $arreglo[1] = $v->getColegio()->getId();  //ok
                    $arreglo[2] =  strtoupper($v->getMatricula()); // // ok
                    $nombre = $v->getNombre();
                    if (empty($nombre)) {
                        $apenom = strtoupper($v->getApellido()); //apellido sólo porque el nombre está vacío
                    } else {
                        $apenom = strtoupper(trim($v->getApellido()) . ', ' . trim($v->getNombre())); // con coma para guardar compatibilidad
                    }


                    $reemplazar = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ä', 'ë', 'ï', 'ö', 'ü');
                    $con    =     array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü');
                    $apenom  = str_replace($reemplazar, $con, $apenom);
                    if ($dbf) { // si es dbf manejo encoding de d.o.s
                        // $apenom = mb_convert_encoding(mb_strtoupper($apenom), "CP850");//LUEGO ABRO EN FOX CON DOS Y SE VE OK
                        //                        $apenom = iconv('UTF-8', 'ISO-8859-1', mb_strtoupper($apenom));//LUEGO ABRO EN FOX CON DOS Y SE VE OK
                        $apenom = mb_convert_encoding(mb_strtoupper($apenom), "CP850", "UTF-8");
                    }
                    $arreglo[3] =  $apenom; //strtoupper($v->getApellido().' '.$v->getNombre());// to do: ver de  reemplazar ',' por espacios 
                    $direccion =  strtoupper($v->getDomicilio());
                    $direccion  = str_replace($reemplazar, $con, $direccion);
                    if ($dbf) { // si es dbf manejo encoding de d.o.s
                        $direccion = mb_convert_encoding($direccion, "CP850", "UTF-8"); //LUEGO ABRO EN FOX CON DOS Y SE VE OK
                    }
                    $arreglo[4] = $direccion; // strtoupper($v->getDomicilio()); // to do: ver de  reemplazar ',' por espacios 
                    $localidad =  strtoupper($v->getLocalidad());
                    $localidad  = str_replace($reemplazar, $con, $localidad);
                    $arreglo[5] =  $localidad; // to do ver si no tiene es - y no queda bien 


                    //  if ($arreglo[5] == '-0' || $arreglo[5] == '-'){
                    //   $arreglo[5] = '-';
                    //  }
                    $arreglo[6] =  strtoupper($v->getLocalidad()->getProvincia()); // to do: traer provincia con la localidad 

                    $arreglo[7] = $v->getEstadoProfesional()->getEstado();
                    if (is_null($v->getDesde())) {
                        $arreglo[8] = $v->getDesde();
                    } else {
                        if ($dbf) {
                            $arreglo[8] = $v->getDesde()->format('Ymd'); //formato d efecha para dbf
                        } else {
                            $arreglo[8] = $v->getDesde()->format('d/m/Y'); //formato d efecha para csv
                        }
                    }
                    if (is_null($v->getHasta())) {
                        $arreglo[9] = $v->getHasta();
                    } else {
                        if ($dbf) {
                            $arreglo[9] = $v->getHasta()->format('Ymd');   //formato d efecha para dbf
                        } else {
                            $arreglo[9] = $v->getHasta()->format('d/m/Y'); //formato d efecha para csv
                        }
                    }
                    if (is_null($v->getFechaActualizacion())) {
                        $arreglo[10] = $v->getFechaActualizacion();
                    } else {
                        if ($dbf) {
                            $arreglo[10] = $v->getFechaActualizacion()->format('Ymd'); // date('Ymd');   //formato d efecha para dbf          
                        } else {
                            $arreglo[10] = $v->getFechaActualizacion()->format('d/m/Y'); //formato d efecha para csv
                        }
                    }
                    if (is_null($v->getFechaActualizacion())) {
                        $arreglo[11] = '00:00';
                    } else {
                        $arreglo[11] = $v->getFechaActualizacion()->format('H:i'); //ver de donde sale, que no guardamos hora actualizacion...iol tampoco
                    }

                    if ($dbf) { //agrego el renglon en el dbf o csv
                        dbase_add_record($db, $arreglo);
                    } else {
                        fputcsv($outputBuffer, $arreglo);
                    }
                }
            } //if de estado    
        } //endfor cada profesional

        //cierro el  archivo correspondiente
        if ($dbf) {
            if ($claves) {
                dbase_close($db2);
            } else {
                dbase_close($db);
            }
        } else {
            if ($claves) {
                fclose($outputBuffer2);
            } else {
                fclose($outputBuffer);
            }
        }
        return $cantidad;
    }

    /**
     * Realiza la exportación masiva de novedades de Matriculas y Claves
     * @return bool Retorna true si no hubo novedades. false en caso contrario
     */
    public function exportarNovedades(): bool
    {
        $sinNovedades = true;
        // Creo las fechas para realizar búsqueda de novedades
        $fechaDesde = (new \DateTime('now', new \DateTimeZone('America/Argentina/Cordoba')))->modify('-1 day')->modify("midnight")->format('\'Y-m-d H:i:s\'');
        $fechaHasta = (new \DateTime('now', new \DateTimeZone('America/Argentina/Cordoba')))->format('\'Y-m-d H:i:s\'');

        // Obtengo las circunscipciones y colegios para recuperar modificaciones en profesionales
        $circunscripciones = $this->_CircunscripcionRepository->findAllArray();
        $colegios = $this->_ColegioRepository->findAllArray();
        $textoNovedadesMatriculados = '';
        $textoNovedadesClaves = '';

        foreach ($colegios as $colegio) {
            foreach ($circunscripciones as $circunscripcion) {
                // Obtengo las descripciones de colegio y circunscripcion
                $colegioDesc = $this->traeColegio($colegio['id']);
                $circunscripcionDesc = $this->traeCircunscripcion($circunscripcion['id']);

                // Obtengo las matriculas modificadas
                $matriculasAExportar = $this->_profesionalRepository->findNovedadesParaExportacion($circunscripcion['id'], $colegio['id'], $fechaDesde, $fechaHasta);
                $cantidadMatriExpor = 0;
                $pathMatricuDBF ='';
                if (!is_null($matriculasAExportar) && !empty($matriculasAExportar)) {
                    //pone los profesionales en archivo dbf o csv
                    //traer todo el colegio
                    //$coreConfigItem2 = $this->_profesionalRepository->findByColegioCircunscripcion($colegio['id'], $circunscripcion['id'],); 
                    //$cantidadMatriExpor = $this->outputCSV($coreConfigItem2, $colegio['id'], $circunscripcion['id'], true, false);
                    //if ($cantidadMatriExpor > 0) {
                    //    $pathMatricuDBF = sprintf('%s/exportados/colegio%d_%d.dbf', getcwd(), $colegio['id'], $circunscripcion['id']);
                    //}
                    if ($textoNovedadesMatriculados <> ''){
                        $textoNovedadesMatriculados = $textoNovedadesMatriculados . ', ';
                    }
                    $textoNovedadesMatriculados = $textoNovedadesMatriculados. ' ' . $circunscripcionDesc . '-' .  $colegioDesc . ' ' ;
                    if ($sinNovedades){
                        $sinNovedades = false;
                    }
                }

                // Obtengo las claves modificadas
                $clavesAExportar = $this->_profesionalRepository->findNovedadesParaExportacion($circunscripcion['id'], $colegio['id'], $fechaDesde, $fechaHasta, true);
                $cantidadClavesExpor = 0;
                $pathClavesDBF ='';
                if (!is_null($clavesAExportar) && !empty($clavesAExportar)) {
                    //pone las claves en archivo dbf o csv
                     //traer todo el colegio
                   //  $coreConfigItem2 = $this->_profesionalRepository->findByColegioCircunscripcion($colegio['id'], $circunscripcion['id'],); 
                     //
                   // $cantidadClavesExpor = $this->outputCSV($coreConfigItem2, $colegio['id'], $circunscripcion['id'], true, true);
                   // if ($cantidadClavesExpor > 0) {
                   //     $pathClavesDBF = sprintf('%s/exportados/colpassw%d_%d.dbf', getcwd(), $colegio['id'], $circunscripcion['id']);
                   // }
                    if ($textoNovedadesClaves <> ''){
                        $textoNovedadesClaves = $textoNovedadesClaves . ', ';
                    }                  
                    $textoNovedadesClaves = $textoNovedadesClaves . ' ' . $circunscripcionDesc . '-' .  $colegioDesc . ' ' ;
                    if ($sinNovedades){
                        $sinNovedades = false;
                    }                   
                }

               // if ($cantidadMatriExpor > 0 || $cantidadClavesExpor > 0) {
                    // Envío correo de esta novedad
               //     $this->enviarEmailConNovedades($circunscripcionDesc, $colegioDesc, $cantidadMatriExpor, $pathMatricuDBF, $cantidadClavesExpor, $pathClavesDBF);

            }//circuscripcion
        }//colegios
        $textoNovedadesMatriculados = $textoNovedadesMatriculados . '.-';
        $textoNovedadesClaves = $textoNovedadesClaves . '.-';


        if (!$sinNovedades){//si tuvo novedades tb exporto y mando los completos 
                $pathMatricuDBF ='';
                $coreConfigItem2 = $this->_profesionalRepository->findByColeCir(99, 0, false,false); 
                $cantidadMatriExpor = $this->outputCSV($coreConfigItem2, 99, 0, true, false);     
                $pathMatricuDBF = sprintf('%s/exportados/colegio%d_%d.dbf', getcwd(), 99, 0);

                // Obtengo las claves modificadas
                $pathClavesDBF =''; 
                $coreConfigItem2 = $this->_profesionalRepository->findByColeCir(99, 0, false,true); 
                $cantidadClavesExpor = $this->outputCSV($coreConfigItem2, 99, 0, true, true);
                $pathClavesDBF = sprintf('%s/exportados/colpassw%d_%d.dbf', getcwd(), 99, 0);

                // Envío correo de esta novedad
                $this->enviarEmailConNovedades('TODAS', 'TODOS', $cantidadMatriExpor, $pathMatricuDBF, $cantidadClavesExpor, $pathClavesDBF,$textoNovedadesMatriculados,$textoNovedadesClaves);
        }//si tuvo novedades

        return $sinNovedades;
    }

    /**
     * Envía mail con novedades como adjuntos
     * @param string Descripción de la circunscripción
     * @param string Descripción del colegio
     * @param int Cantidad de matrículas exportadas
     * @param string Path del DBF de matrículas
     * @param string Cantidad de claves exportadas
     * @param int Path del DBF de claves
     */
    private function enviarEmailConNovedades(string $circunscripcion, string $colegio, int $cantidadMatriExpor, string $pathMatricuDBF = null, int $cantidadClavesExpor, string $pathClavesDBF = null,string $textoNovedadesMatriculados = null,string $textoNovedadesClaves = null): void
    {
        $fromAdrress = $_ENV['MAIL_FROM'];
        // $toAdrress = $_ENV['RECIPIENTS_NOVEDADES_NOTIFICACIONS'];
        $toAdrress = explode(',', $_ENV['RECIPIENTS_NOVEDADES_NOTIFICACIONS']); 
        // $toAdrressComplete = $_ENV['RECIPIENTS_NOVEDADES_NOTIFICA_COMPLETE'];
        $email = new TemplatedEmail();

        if ($circunscripcion == 'TODAS'){

          //  $pathMatricuDBF2 = sprintf('%s/exportados/matricu.dbf', getcwd());
          //  copy($pathMatricuDBF, $pathMatricuDBF2);
          //  $pathClavesDBF2 = sprintf('%s/exportados/matpassw.dbf', getcwd());
          //  copy($pathClavesDBF, $pathClavesDBF2);
           
            foreach($toAdrress as $destinatario) {
                $email->addTo(trim($destinatario));
            }
            $email->from($fromAdrress)
                ->subject('Novedades del Administrador de Profesionales -')
                ->htmlTemplate('exportado/mail_novedades.html.twig')                    
                ->context([
                    'novedadesmatriculados' => $textoNovedadesMatriculados,
                    'novedadesclaves' => $textoNovedadesClaves,
                ]);
                
                if ($cantidadMatriExpor > 0) {
                   $email->attachFromPath($pathMatricuDBF,'matricu.dbf'); 
                   
                }
                if ($cantidadClavesExpor > 0) {
                    $email->attachFromPath($pathClavesDBF,'colpassw.dbf');    
                }              
                               
            $this->_mailer->send($email);        
        }else{
         /*   if ($cantidadMatriExpor > 0) {
                $email->from($fromAdrress)
                    ->to($toAdrress)             
                    ->subject(sprintf('Novedades Administrador de Profesionales - %s de %s', $colegio, $circunscripcion))
                    ->htmlTemplate('exportado/mail_novedades.html.twig')
                    ->attachFromPath($pathMatricuDBF);
            }
            if ($cantidadClavesExpor > 0) {
                $email->from($fromAdrress)
                    ->to($toAdrress)
                    ->subject(sprintf('Novedades Administrador de Profesionales - %s de %s', $colegio, $circunscripcion))
                    ->htmlTemplate('exportado/mail_novedades.html.twig')
                    ->attachFromPath($pathClavesDBF);
            }
            */
        }
       // $this->_mailer->send($email);
    }//function enviarEmailConNovedades
}//class ExportadoService
