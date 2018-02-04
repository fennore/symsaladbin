<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractSmartController
{
    /**
     * Matches /.
     *
     * @Route("/", name="intro", schemes="https", defaults={"_format": "html"})
     */
    public function intro()
    {
        return $this->smartRender('default/intro.html.twig');
    }
}
