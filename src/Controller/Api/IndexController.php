<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route(
     *      "/api",
     *      name="api_index",
     *      defaults={"_format": "json"},
     *      requirements={"_format": "json"})
     */
    public function index()
    {
        // @todo display API index? One page leads to all?
        return $this->json('Hello Api');
    }
}
