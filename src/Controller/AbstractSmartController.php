<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract controller with some extra smart layer.
 */
class AbstractSmartController extends AbstractController
{
    protected function smartRender(string $view, array $parameters = array(), $response = null): Response
    {
        
        $response = $this->render($view, $parameters, $response);
        if(preg_match('/.+\.html(\..+)?$/i', $view) || true) {
            $response->headers->set('content-type', 'text/html');
        }
        return $response;
    }
}
