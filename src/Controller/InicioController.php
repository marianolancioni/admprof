<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 /**
 * InicioController
 * 
 * @author Gustavo Muller <gmuller@justiciasantafe.gov.ar>
 * 
 */
class InicioController extends AbstractController
{
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('inicio/index.html.twig', ['controller_name' => 'Administración de Profesionales']);
    }
}
