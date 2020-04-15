<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract controller with some extra smart layer.
 */
class AbstractSmartController extends AbstractController
{
    /**
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    protected function smartRender(string $view, array $parameters = array(), Response $response = null): Response
    {
        $response = $this->render($view, $parameters, $response);
        if (preg_match('/.+\.html(\..+)?$/i', $view)) {
            $response->headers->set('content-type', 'text/html');
        }

        return $response;
    }
}
