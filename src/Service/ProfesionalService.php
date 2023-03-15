<?php

namespace App\Service;

use App\Repository\ColegioCircRepository;
use App\Repository\ProfesionalRepository;
use App\Repository\ColegioRepository;
use App\Repository\CircunscripcionRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\ORM\EntityManagerInterface; 
use Doctrine\Persistence\ManagerRegistry;


/**
 * ProfesionalService
 * 
 * Servicio utilizado para realizar validaciones y cálculos
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 */
class ProfesionalService
{
    private $_colCircRepository;
    private $_profesionalRepository;
    private $_ColegioRepository;
    private $_CircunscripcionRepository;
    private $_doctrine;

    public function __construct(ManagerRegistry $doctrine, ColegioCircRepository $colCircRepository, ProfesionalRepository $profesionalRepository, ColegioRepository $ColegioRepository,  CircunscripcionRepository $CircunscripcionRepository)
    {
        $this->_colCircRepository =  $colCircRepository;
        $this->_profesionalRepository =  $profesionalRepository;
        $this->_ColegioRepository =  $ColegioRepository;
        $this->_CircunscripcionRepository =  $CircunscripcionRepository;
        $this->_doctrine = $doctrine;
    }

    public function traeClaveprof($id)//trae clave profesional
    {
        $coreConfigItem = $this->_profesionalRepository->find($id);//
        return $coreConfigItem->getClave();      
    }   

    public function traeColegios()//trae todos los colegios como un array, NO como objetos
    {
        $coreConfigItem = $this->_ColegioRepository->findAllArray();//
        return $coreConfigItem;      
    }

    public function traeCircunscripciones()//trae todas las circunscripciones como un array, NO como objetos
    { 
        $coreConfigItem = $this->_CircunscripcionRepository->findAllArray();// 
        return $coreConfigItem;      
    }


    public function traeColegio($id)//trae el colegio del id, si es id 99 es todos los colegios (para pantallas de expostacion)
    {   
        if ($id <> 99){
            $coreConfigItem = $this->_ColegioRepository->find($id);//
            return $coreConfigItem->getColegio();
        }else{
            return "TODOS";
        }      
    }

    public function traeCircunscripcion($id)//trae circunscripción, si es cero es todas (para pantallas de expostacion)
    { 
        if ($id <> 0){
            $coreConfigItem = $this->_CircunscripcionRepository->find($id);//
            return $coreConfigItem->getCircunscripcion();      
        }else{
            return "TODAS";
        }   
    }

     
    public function GeneraClaveNum4()// 
    {   
        return rand(1000,9999); 
       // return mt_rand(1000,9999);      
    }

  
    
    function encrypt_decrypt($action, $string)//basico despues definir como manejamos en producción
    {
        $output = false;
        if ($action == 'encrypt') {
            $clave = intval($string);
           // $clave = '5274';
            $output =  $this->Funcion2($clave);//clave char5 sin encoding
            $output =  mb_convert_encoding($output, "UTF-8", "WINDOWS-1252");// asi graba como lo hace indra en oracle 
            // otros encoding...CP850 UTF-8 WINDOWS-1252 AL32UTF8 ASCII
          
           // $output =  mb_convert_encoding($output, "WINDOWS-1252", "CP850");
        } else {
            if ($action == 'decrypt') { 
               // $string2 =  mb_convert_encoding( $string, "CP850");
                $string = mb_convert_encoding($string, "WINDOWS-1252", "UTF-8");
                $output = $this->Funcion3($string);//numerico de 4
            }
        }
      
        return $output;
    }


    function encripta_desencripta($action, $string)//con archivos de clave publica y privada
    {
        /* =================================================
         * encripta_desencripta usando clave publica y privada de archivo
         * =================================================
         */
        require_once '../vendor/autoload.php';//una opcion para manejo de archivos se requiere para usar filesystem object
        $fsObject = new Filesystem(); //objeto para trabajar con carpetas y archivos.
       // $current_dir_path = getcwd();//directorio de trabajo.
        $current_dir_path = '../resources';//para no dejarlo en el public que es peligroso
        //return $current_dir_path ;

        //crea el directorio claves tomando como base el directorio de trabajo.
        try {
            $new_dir_path = $current_dir_path . "/claves";
        
            if (!$fsObject->exists($new_dir_path))
            {              
                $fsObject->mkdir($new_dir_path, 0775);// 0775 ignorado en windows, asigna permisos al directorio en linux       
            }
        } catch (IOExceptionInterface $exception) {
            echo "Error creating directory at". $exception->getPath();
        }

        $output = false;        
      
        if ($action == 'encrypt') {
            //$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
            $publicKey =  openssl_pkey_get_public(file_get_contents($new_dir_path . '/publicax.key')); //clave publica encripta, no sirve para desencriptar          
            $a_key = openssl_pkey_get_details($publicKey);//arreglo con los datos de la clave publica
            $publicKey1 ='';
            $publicKey1 = $a_key['key'];  //clave publica extraida del arreglo             
            openssl_public_encrypt($string, $crypttext, $publicKey1);//encripto string con publicKey1 devolciendo $crypttext
            $output = $crypttext; 
        } else {
            if ($action == 'decrypt') {
                //$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                $privateKey = openssl_pkey_get_private(file_get_contents($new_dir_path . '/privadax.key')); //clave privada desencripta lo encriptado con la publica         
                $privatekey1 ='';
                openssl_pkey_export($privateKey, $privatekey1);  //de la clave privado del archivo extraigo el string de la clave                 
                openssl_private_decrypt($string, $decrypted, $privatekey1);// desencripto string con clave provada privatekey1 devolviendo decrypted
                $output = $decrypted; 
            }
        }
        return $output;
    }
  


    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar
    // abajo servicios sólo para desarrollo/test luego quitar





    public function Funcion3( $clave)//con una clave char 5 recupera elnum 4
    {   

        $Ar[0] = 174;
        $Ar[1] = 177;
        $Ar[2] = 180;
        $Ar[3] = 183;
        $Ar[4] = 186;
        $numero = "";                
        $desencrip = ""; 

        for ($i = 0; $i < strlen($clave); $i++){

            $numero =  substr($clave,$i, $i + 1);
            $car = $this->charAt($numero,0);
            $codigoAscii = ord($car);
            $intCase = $codigoAscii - $Ar[$i];
     
            switch ($intCase){
            
                case 0:
                    $desencrip .= "0";
                    break;
                case 1:
                    $desencrip .= "1";
                     break;
                case 2:
                    $desencrip .= "2";
                     break;
                case 3:
                    $desencrip .= "3";
                     break;
                case 4:
                    $desencrip .= "4";
                     break;
                case 5:
                    $desencrip .= "5";
                     break;
                case 6:
                    $desencrip .= "6";
                     break;
                case 7:
                    $desencrip .= "7";
                     break;
                case 8:
                    $desencrip .= "8";
                     break;
                case 9:
                    $desencrip .= "9";
                     break;
            }
         }                   
         return intval($desencrip);      
    }


    //emula fun2 del progrma de fox, estaría completo fun 2 parece reproducible 100%
    public function Funcion2( $clave)//con un numerico de 8 OJO o 10??y un caracter en blanco..Define la clave entre 1000 y 9999)
    {   
        if ($clave < 1000 || $clave > 9999)    {
            return 'ERROR';
        }
        $Z = intval(fmod($clave*5/4,5));
        $a = 225-$Z; 
        $p1char = strval($clave); // LA CLAVE EN CARACTER DE 4
             
        switch ($Z) {
            case 0:
                $m_lpassw = chr($a+5) . $p1char;
                break;
            case 1:
                $m_lpassw = substr($p1char,0,1) . chr($a+1) . substr($p1char,1,3);
                break;
            case 2:
                $m_lpassw = substr($p1char,0,2) . chr($a+3) . substr($p1char,2,2);
                break;
            case 3:
                $m_lpassw = substr($p1char,0,3) . chr($a+2) . substr($p1char,3,1);
                break;   
            case 4:
                $m_lpassw = $p1char . chr($a+4);
                break;                                
        }
       
        $m_encrip = '';
       // $cadenaaux= '';
        $i = 2;
        do { 
           $XX = fmod(ord(substr($m_lpassw,$i-2,1)) +($i*3),255);                             
           if ($XX < 120){
                   $XX = $XX + 120;
           }	          
           //$cadenaaux=  $cadenaaux . strval($XX) . '_';
           $m_encrip = $m_encrip . CHR($XX);
           $i = $i + 1; 									   
        } while ($i <= 6);
        return $m_encrip;   //     
        //return $cadenaaux;
        //return chr(154);
    }


    public function Funcion1b($seed)//con una clave char 5 recupera elnum 4
    {   
        $strSEED = "1111111";
        $A[1]   = 34564;
        $A[2]   = 78755;
        $A[3]   = 93485;
        $A[4]   = 93856;
        $A[5]   = 82395;
        $A[6]   = 12597;
        $A[7]   = 95900;
        $A[8]   = 94866;
        $A[9]   = 92750;
        $A[10]  = 11111;
        $A[11]  = 36564;
        $A[12]  = 78723;
        $A[13]  = 93435;
        $A[14]  = 93245;
        $A[15]  = 82395;
        $A[16]  = 12457;
        $A[17]  = 93500;
        $A[18]  = 93566;
        $A[19]  = 92750;
        $A[20]  = 11351;
        $A[21]  = 32564;
        $A[22]  = 78745;
        $A[23]  = 96885;
        $A[24]  = 69856;
        $A[25]  = 82365;
        $A[26]  = 12152;
        $A[27]  = 38652;
        $A[28]  = 20946;
        $A[29]  = 92643;
        $A[30]  = 14631;
        $A[31]  = 34535;
        $A[32]  = 75345;
        $A[33]  = 93255;
        $A[34]  = 93254;
        $A[35]  = 82351;
        $A[36]  = 12361;
        $A[37]  = 15430;
        $A[38]  = 13866;
        $A[39]  = 15350;
        $A[40]  = 11231;
        $A[41]  = 34523;
        $A[42]  = 78254;
        $A[43]  = 93486;
        $A[44]  = 98566;
        $A[45]  = 82494;
        $A[46]  = 14777;
        $A[47]  = 93600;
        $A[48]  = 94256;
        $A[49]  = 92750;
        $A[50]  = 11361;
        $A[51]  = 34565;
        $A[52]  = 73565;
        $A[53]  = 93485;
        $A[54]  = 93456;
        $A[55]  = 85695;
        $A[56]  = 12567;
        $A[57]  = 95600;
        $A[58]  = 91266;
        $A[59]  = 12750;
        $A[60]  = 11431;
        $A[61]  = 34564;
        $A[62]  = 78755;
        $A[63]  = 93485;
        $A[64]  = 92556;
        $A[65]  = 82395;
        $A[66]  = 12537;
        $A[67]  = 95230;
        $A[68]  = 98666;
        $A[69]  = 92750;
        $A[70]  = 11134;
        $A[71]  = 34564;
        $A[72]  = 78755;
        $A[73]  = 94485;
        $A[74]  = 93855;
        $A[75]  = 86695;
        $A[76]  = 17797;
        $A[77]  = 95988;
        $A[78]  = 99966;
        $A[79]  = 90050;
        $A[80]  = 12211;
        $A[81]  = 34334;
        $A[82]  = 78744;
        $A[83]  = 95585;
        $A[84]  = 93666;
        $A[85]  = 82775;
        $A[86]  = 12887;
        $A[87]  = 99900;
        $A[88]  = 97766;
        $A[89]  = 92460;
        $A[90]  = 11341;
        $A[91]  = 34564;
        $A[92]  = 78115;
        $A[93]  = 93422;
        $A[94]  = 33856;
        $A[95]  = 82445;
        $A[96]  = 12555;
        $A[97]  = 95520;
        $A[98]  = 94864;
        $A[99]  = 92350;
        $A[100] = 98763;

        $sub = substr($strSEED,0,5);
        $sub2 = substr($strSEED,4);
        
        $int1 = intval($sub);
        $int2 = intval($sub2);

        $p1 = ($int1 * $int2) / 3;
        $p2 = " ";


       // $seed1 = Math.abs(Math.sin(p1)) * p2.charAt(0);
       $seed1 = abs(sin($p1)) * intval($this->charAt($p2,0));
       $diferencia = $this->Dif($p1,$A);
       $rnd = $this->Rnd(floatval($seed1));
       $ret = intval(($rnd / $A[$diferencia]) * 1000000000);
     //  $ret = intval(($rnd / 2345) );

      //  mt_srand(abs(sin($p1))*32);
      //  $randval = mt_rand();
       // return $randval;  
       // return  $this->Dif($p1,$A);//ok
      // return  abs(sin($p1))*32; // 
     //    return $randval;
      //   $RET = intval( ($randval) / $this->Dif($p1,$A)*1000000000);

        while ($ret < 1000 || $ret > 9999):
            if ($ret <= 999){
                $ret = intval($ret * 1.1);
            }else{
                $ret = intval($ret * 0.9);
            }  
        endwhile;     
        
        return $ret;

    }


     //emula fun1 del progrma de fox, NO estaría completo, no podemos reproducir la función de fox  RAND.
    public function Funcion1( $semilla, $caracter)//con un numerico de 8 OJO o 10??y un caracter en blanco..Define la clave entre 1000 y 9999)
    {   
        //PA1 = STR(PR1)  $rest = substr("abcdef", 2, -1);  // devuelve "cde"
        $pa1 = strval($semilla);
        if (intval(substr($pa1,0,5)) == 0 || intval(substr($pa1,4,6)) == 0){ //IF  VAL(SUBSTR(PA1,1,5)) = 0 .OR. VAL(SUBSTR(PA1,5,6)) = 0
            return 0;   //    return 0 el parametro semilla no es valido
        }           
        //return $pa1; //llega la semilla
        $p1 =  (intval(SUBSTR($pa1,0,3)) * intval(SUBSTR($pa1,2,6))) /3;  //P1 = (VAL(SUBSTR(PA1,1,5))*VAL(SUBSTR(PA1,5,6)))/3        
       //return $p1; //valor p1 procesa con val y substr
        $A[1]   = 34564;
        $A[2]   = 78755;
        $A[3]   = 93485;
        $A[4]   = 93856;
        $A[5]   = 82395;
        $A[6]   = 12597;
        $A[7]   = 95900;
        $A[8]   = 94866;
        $A[9]   = 92750;
        $A[10]  = 11111;
        $A[11]  = 36564;
        $A[12]  = 78723;
        $A[13]  = 93435;
        $A[14]  = 93245;
        $A[15]  = 82395;
        $A[16]  = 12457;
        $A[17]  = 93500;
        $A[18]  = 93566;
        $A[19]  = 92750;
        $A[20]  = 11351;
        $A[21]  = 32564;
        $A[22]  = 78745;
        $A[23]  = 96885;
        $A[24]  = 69856;
        $A[25]  = 82365;
        $A[26]  = 12152;
        $A[27]  = 38652;
        $A[28]  = 20946;
        $A[29]  = 92643;
        $A[30]  = 14631;
        $A[31]  = 34535;
        $A[32]  = 75345;
        $A[33]  = 93255;
        $A[34]  = 93254;
        $A[35]  = 82351;
        $A[36]  = 12361;
        $A[37]  = 15430;
        $A[38]  = 13866;
        $A[39]  = 15350;
        $A[40]  = 11231;
        $A[41]  = 34523;
        $A[42]  = 78254;
        $A[43]  = 93486;
        $A[44]  = 98566;
        $A[45]  = 82494;
        $A[46]  = 14777;
        $A[47]  = 93600;
        $A[48]  = 94256;
        $A[49]  = 92750;
        $A[50]  = 11361;
        $A[51]  = 34565;
        $A[52]  = 73565;
        $A[53]  = 93485;
        $A[54]  = 93456;
        $A[55]  = 85695;
        $A[56]  = 12567;
        $A[57]  = 95600;
        $A[58]  = 91266;
        $A[59]  = 12750;
        $A[60]  = 11431;
        $A[61]  = 34564;
        $A[62]  = 78755;
        $A[63]  = 93485;
        $A[64]  = 92556;
        $A[65]  = 82395;
        $A[66]  = 12537;
        $A[67]  = 95230;
        $A[68]  = 98666;
        $A[69]  = 92750;
        $A[70]  = 11134;
        $A[71]  = 34564;
        $A[72]  = 78755;
        $A[73]  = 94485;
        $A[74]  = 93855;
        $A[75]  = 86695;
        $A[76]  = 17797;
        $A[77]  = 95988;
        $A[78]  = 99966;
        $A[79]  = 90050;
        $A[80]  = 12211;
        $A[81]  = 34334;
        $A[82]  = 78744;
        $A[83]  = 95585;
        $A[84]  = 93666;
        $A[85]  = 82775;
        $A[86]  = 12887;
        $A[87]  = 99900;
        $A[88]  = 97766;
        $A[89]  = 92460;
        $A[90]  = 11341;
        $A[91]  = 34564;
        $A[92]  = 78115;
        $A[93]  = 93422;
        $A[94]  = 33856;
        $A[95]  = 82445;
        $A[96]  = 12555;
        $A[97]  = 95520;
        $A[98]  = 94864;
        $A[99]  = 92350;
        $A[100] = 98763;
      //  return abs(sin($p1))*8;
       mt_srand(abs(sin($p1))*32);
       $randval = mt_rand();
      // return $randval;  
      // return  $this->Dif($p1,$A);//ok
     // return  abs(sin($p1))*32; // 
        return $randval;
        $RET = intval( ($randval) / $this->Dif($p1,$A)*1000000000);//ver como cambio ord qu es asscii por ansi
       // $RET = intval( $aux2 / $this->Dif($p1,$A)*1000000000);      
        //RET = INT( (    RAND(  ABS(SIN(P1))*ASC(P2)  )   )   /DIF(P1)*1000000000 )

        while ($RET < 1000 || $RET > 9999):
            if ($RET <= 999){
                $RET = intval($RET * 1.1);
            }else{
                $RET = intval($RET * 0.9);
            }  
        endwhile;     
        
        return $RET;
        //return 5678;//SI RECORRIO TODO Y NO SALIO ES VALIDO   
    }//funcion genera clave  numerica de 4  


    public function Dif($p,$A)// 
    {          
       // $Z = IIF(ABS(intval($p/100)-($p/100))*100=0,100,ABS(intval($p/100)-($p/100))*100); // Z = IIF(ABS(INT(P/100)-(P/100))*100=0,100,ABS(INT(P/100)-(P/100))*100)
        if (ABS(intval($p/100)-($p/100))*100==0){  
            $Z = 100;
        }else {
            $Z = ABS(intval($p/100)-($p/100))*100;
        }
        if ($Z < 1){
            $Z = $Z+1;
        }       
        return $A[$Z];                 
    }//funcion dif 

    function charAt($str,$pos) {
        return (substr($str,$pos,1) !== false) ? substr($str,$pos,1) : -1;
    }


    public function Rnd($number){ //number es la semilla en flotante
            
        if($number == 0.0)
        {
                return $this->Prev($number);
        } 
        else if($number > 0.0)
        {
                return $this->Next(intval($number), true);
        }   
        
        return 0;        
    }

    public function Prev($number)
    {        
            return (float)((($number) / 2147483648.0));           
    }

    // Get the next value in sequence.
    public function Next(int $prevSeed, $update)
    {
                        
           $value = ((($prevSeed) * 1103515245 + 12345) & 0x7FFFFFFF);            
            if($update)
            {
                 //   $seed = $value;
            }
            return (float)((((double)$value) / 2147483648.0)); 
    }

    /**
     * Importa profesionales desde IOL (Producción)
     * 
     * @param $circunscripcion  ID Circunscripción a la que están vinculados los Profesionales a Importar
     * @param $colegio          ID Colegio al que pertenecen los Profesionales a Importar
     * 
     */
    public function importFromIol(int $circunscripcion, int $colegio)
    {
        $filtroIOL = '';
        $filtroAdmprof = '';
        // Evalúa Filtro por Circunscripción (0 = TODAS)
        if ($circunscripcion) {
            $filtroIOL .= " and PRO_CIRCUNSCRIPCION = $circunscripcion";
            $filtroAdmprof .= " and circunscripcion_id = $circunscripcion";
        }

        // Evalúa filtro por Colegio (99 = TODOS)
        if ($colegio != 99) {
            $filtroIOL .= " and PRO_COLEGIO = $colegio";
            $filtroAdmprof .= " and colegio_id = $colegio";
        }

        // Si hay algún filtro (colegio, circunscripción o ambos, construye cláusula WHERE)
        if ($filtroIOL) {
            $filtroIOL = 'WHERE ' . substr($filtroIOL, 5);
            $filtroAdmprof = 'WHERE ' . substr($filtroAdmprof, 5);
        }

        // Establece conectores a BD Oracle y la propia de este Sistema
        $em = $this->_doctrine->getManager();
        $iol = $this->_doctrine->getManager('iol');

        // Obtiene todos los profesionales conforme a los Filtro indicados
        $profesionales = $iol->getConnection()->prepare("SELECT * FROM inet_capa.profesional $filtroIOL")->executeQuery()->fetchAllAssociative();

        if (count($profesionales)) {
            // BORRA TODOS LOS REGISTROS EN ENTIDAD DE PROFESIONAL PARA LA CIRCUNSCRIPCIÓN Y COLEGIO INDICADOS!
            $em->getConnection()->prepare("DELETE FROM profesional $filtroAdmprof")->executeQuery();
        }

        //Comienza a construir el INSERT Sql
        $lineaBase = 'INSERT INTO public.profesional(id, circunscripcion_id, colegio_id, localidad_id, estado_profesional_id, matricula, apellido, nombre, ';
        $lineaBase .= 'numero_documento, domicilio, correo, desde, hasta, clave, estado, fecha_actualizacion)';
        $lineaBase .= " VALUES (nextval('profesional_id_seq'),";

        $conta = 0;
        foreach ($profesionales as $profesional) {
            
            // Saltea registro si la circunscripción no es válida (hay un caso en la BD de IOL)
            if ((int) ($profesional['PRO_CIRCUNSCRIPCION']) > 5)
                continue;

            $sqlInsert = $lineaBase;
            $sqlInsert .= (int) ($profesional['PRO_CIRCUNSCRIPCION']) . ', ';
            $sqlInsert .= (int) ($profesional['PRO_COLEGIO']) . ', ';
            if ($profesional['LOC_ID']) {
                $sqlInsert .= $profesional['LOC_ID'] . ', ';
            } else {
                $sqlInsert .= '866, '; // Localidad nula
            }
            $sqlInsert .= $profesional['PRO_ESTADO'] + 1 . ', ';
            $sqlInsert .= "'" . $profesional['PRO_MATRICULA'] . "', ";

            // Apellido y Nombre: Separa el nombre en Apellido y Nombre y le saca el formato de mayúsculas. Escapa comillas simples para que no genere error
            $nombre = ucwords(mb_strtolower(str_replace("'", "''", $profesional['PRO_NOMBRE'])));
            if (strpos($nombre, ',')) {
                $sqlInsert .= "'" .substr($nombre, 0, strpos($nombre, ',')) . "', ";
                $sqlInsert .= "'" . trim(substr($nombre, strpos($nombre, ',') +1)) . "', ";
            }
            else {
                $sqlInsert .= "'" . $nombre . "', ";
                $sqlInsert .= "'" . "', ";
            }
            if ($profesional['PRO_NRODOC']) {
                $sqlInsert .= $profesional['PRO_NRODOC'] . ', ';
            } else {
                $sqlInsert .= 'null, ';
            }
            // Domicilio: le saca el formato de mayúsculas. Escapa comillas simples para que no genere error
            $sqlInsert .= "'" . ucwords(mb_strtolower(str_replace("'", "''", $profesional['PRO_DOMICILIO']))) . "', ";
            $sqlInsert .= "'" . $profesional['PRO_CORREO'] . "', ";
            if ($profesional['PRO_FECHA_DESDE']) {
                $sqlInsert .= "'" . $profesional['PRO_FECHA_DESDE'] . "', ";
            } else {
                $sqlInsert .= 'null, ';
            }
            if ($profesional['PRO_FECHA_HASTA']) {
                $sqlInsert .= "'" . $profesional['PRO_FECHA_HASTA'] . "', ";
            } else {
                $sqlInsert .= 'null, ';
            }
            $sqlInsert .= "'" . $profesional['PRO_CLAVE'] . "', ";
            $sqlInsert .= $profesional['ESTADO'] . ', ';
            if ($profesional['FECHA_ACTUALIZACION']) {
                $sqlInsert .= "'" . $profesional['FECHA_ACTUALIZACION'] . "'";
            } else {
                $sqlInsert .= 'null';
            }
            $sqlInsert .= ');';

            try {
                $em->getConnection()->prepare($sqlInsert)->executeQuery();
                $conta++;
            }
            catch (\Exception $e) {
                return [-1, $conta, $e]; // Retorna -1 como indicador de error junto con los registros que alcanzó a procesar y detalles de la Excepción ocurrida
            }
        }

        return [$conta]; // Retorna cant. de Registros procesados
    }

}
