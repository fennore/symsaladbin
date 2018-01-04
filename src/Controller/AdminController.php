<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function overview()
    {
        // replace this line with your own code!
        return $this->render("default/admin/overview.html.twig");
    }

}
