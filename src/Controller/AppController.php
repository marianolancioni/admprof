<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AppService;

/**
 * AppController
 * 
 * Controlador genérico a Nivel de Aplicación para invocar Servicios y
 * renderizar vistas parciales vía XmlHttpRequest (petición Ajax)
 * 
 * @author Martín Maglianesi <mmaglianesi@justiciasantafe.gov.ar>
 * 
 */
class AppController extends AbstractController
{
    /**
     * Renderiza mensajes de notificación del tipo "Flash Messages" de Symfony
     */
    #[Route('/app_messages', name: 'app_flashes_messages')]
    public function flashesMessages(): Response
    {
        return new Response($this->renderView('components/_flashesMessages.html.twig'));
    }

    /**
     * Envía un correo con información del código de error, su traza 
     * completa y se complementa con datos del usuario y su dirección IP
     */
    #[Route('/app_error_notification', name: 'app_error_notification' )]
    public function errorNotification(Request $request, AppService $appService): Response
    {
        // Se dispara correo de notificación de error únicamente para errores con usuarios autenticados y en ambiente productivo
        if ($_ENV['APP_ENV'] == 'prod' && $this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($appService->errorNotification($request)) {
                return new Response(null, 204);
            } else {
                return new Response(null, 500); // Retorno 500 si falló el envío del correo
            }
        }
        return new Response(null, 204); // Respuesta por defecto
    }

}
