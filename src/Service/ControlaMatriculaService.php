<?php


namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

use App\Repository\ProfesionalRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

use App\Entity\ColegioCirc;
use App\Entity\Colegio;
use App\Entity\Circunscripcion;
use App\Repository\ColegioCircRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ExportadoService
 * 
 * Servicio utilizado para realizar validaciones y cálculos sobre Matrículas de Profesionales
 * 
 * @author María Luz Bedini <mlbedini@justiciasantafe.gov.ar>
 */


 class ControlaMatriculaService extends ProfesionalService
{
    private $_colCircRepository;
    private $_profesionalRepository;
    
   
    public function __construct(ColegioCircRepository $colCircRepository, ProfesionalRepository $profesionalRepository)
    {
        $this->_colCircRepository =  $colCircRepository;
        $this->_profesionalRepository =  $profesionalRepository;
    }
    

    public function ValidaMatricula($matricu,$cole,$circ)//recibe cadena de caracteres a validar, colegio y circunscripción
{   
    
    $coreConfigItem = $this->_colCircRepository->findByColeCirc($cole, $circ);//recupera instancia de colegiocirc
    

    if ($coreConfigItem) {            
        $validos =  $coreConfigItem->getCaracteresPermitidos();  //caracteres validos además de los númericos siempre permitodos
    
    } else {
        $validos = '';// no tiene configurado
    }   

    if ($matricu == ''){ //No valido, si es vacía la matrícula es inválida
        return false;
    }     
    
    for($i=0;$i<strlen($matricu);$i++)//recorro cada caracter de la matricula para validarlo caracvter por caracter
    {
        $c = $matricu[$i];// c es el caracter a validar
        $cEsValido = 'NO';
        if ($c == '1' || $c == '2' || $c == '3' || $c == '4' || $c == '5' || $c == '6' || $c == '7' || $c == '8' || $c == '9' || $c == '0') {
            $cEsValido = 'SI'; //si es un numero es valido
        }else {    
            if ($validos == ''){
                //no hay nada cargado en colegio circ o esta cargado como vacío : solo admito numericos y no es numerico
                return false;
            }else {    //tengo cargados caracteres validos en colegio circ
                for($J=0;$J<strlen($validos);$J++)//recorro la cadena con caracteres validos para ver si coincide con uno valido
                {
                    $v = $validos[$J];
                    if ($c == $v){
                        $cEsValido = 'SI';//to do: salir de este for para optimizar aunque son 5 o 6 iteraciones 
                    }
                }
            }
        }
        if ($cEsValido == 'NO'){  //al primer caracter de la cadena que resulta no valido devuelvo cadena no valida
            return false; // y saldría del bucle por el return
        }else {
            if ($i == strlen($matricu)){  //el caracter es valido y es el ultimo de la cadena implica que la cadena es valida
                return true;
            }
        }
    }
    
    return true;//SI RECORRIO TODO Y NO SALIO ES VALIDO         
}//

public function ValidaMatricuDuplicada($matricu, $cole, $circ, $modo,$idprof)//recibe cadena de caracteres a validar, colegio y circunscripción
    {   
        $cantidad = 0;
        $cantidad = $this->_profesionalRepository->findByMatricuCount($matricu, $cole, $circ);//recupera instancia de PROFESIONALES  
        if ($cantidad > 1){
            return false;
        }else{
            if ($cantidad == 0){
                return true;
            }else{ 
                $coreConfigItem = $this->_profesionalRepository->findByMatricu($matricu, $cole, $circ);//recupera instancia de PROFESIONALES 
                $id2 = 0; 
                $id2 =  $coreConfigItem->getid();  //id edel encontrado
                if ($modo == 'N'){//new
                    if ($cantidad <> 0) {  
                        return false; 
                    }  else{//cantidad = 0
                        return true;
                    }     
                }   
                if ($modo == 'E'){//editar
                    if ($cantidad <> 0 && $idprof <> $id2 ){//id encontrado vs el del qu eestoy editando. ya existe y no es el mismo registro
                        return false;
                    }else {
                        return true;
                    }
                }       
            }
        }
    }
}