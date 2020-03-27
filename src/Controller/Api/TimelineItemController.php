<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TimelineItemController extends AbstractController
{
    /**
     * Clear the images data.
     *
     * @Route("/api/images/all", name="api_images_clear", methods={"DELETE"})
     */
    public function clearImages()
    {
        // empty images list
        // reset images state
        return JsonResponse::create(null, 204);
    }
}
