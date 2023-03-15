<?php

namespace App\Reports;

use Knp\Snappy\Pdf;
use Symfony\Component\Security\Core\Security;

/**
 * Clase Base para la generación de reportes en PDF.
 * Encargada de setear configuraciones comunes a todos los reportes
 * 
 * @author Juani Alarcón <jialarcon@justiciasantafe.gov.ar>
 */
class BasePdf
{

    protected $_pdf;

    /**
     * Constructor
     */
    public function __construct(Pdf $pdf, Security $security)
    {
        $this->_pdf = $pdf;
        $this->_pdf->setTimeout(360);
        // Seteo el usuario al reporte
        //$this->_pdf->setOption('footer-center', $security->getUser()); //ESTA DANDO ERROR AL RECUPERAR EL USUARIO. 
    }

    /**
     * Retorna instancia de objeto Pdf
     * @return Pdf
     */
    public function getPdf(): Pdf
    {
        return $this->_pdf;
    }
}