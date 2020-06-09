<?php

namespace App\Controller\Api;

use App\Lists\TimelineItemPager;
use App\Repository\TimelineItemRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TimelineItemController extends AbstractController
{
    /**
     * Returns a list of images starting from given offset and as many as given length.
     *
     * @Route(
     *      "/api/images/{offset}/{limit}",
     *      name="api_images", methods={"GET", "HEAD"},
     *      requirements={"offset"="\d+","length"="\d+"})
     */
    public function getImages(
        TimelineItemRepository $itemRepo,
        SerializerInterface $serializer,
        int $offset = 0,
        int $limit = 1): Response
    {
        $pager = new TimelineItemPager(
            $offset,
            $limit,
            $this->isGranted('ROLE_ADMIN'),
            $itemRepo
        );

        $json = $serializer->serialize($pager, 'json');

        return JsonResponse::fromJsonString($json);
    }

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
