<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    /**
     * Matches /
     * @Route("/", name="intro", schemes="https")
     */
    public function intro()
    {
        return $this->render('default/intro.html.twig');
    }

}
