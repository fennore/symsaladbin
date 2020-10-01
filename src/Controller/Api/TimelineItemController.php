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
     *      name="api_timelineitems", methods={"GET", "HEAD"},
     *      requirements={"offset"="\d+","length"="\d+"})
     */
    public function getTimelineItems(
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
     * Returns a list of images starting from given offset and as many as given length.
     *
     * @Route("/api/images", name="api_timelineitems_del", methods={"DELETE"})
     */
    public function deleteTimelineItems(
        Request $request,
        TimelineItemRepository $itemRepo): Response
    {
        $content = json_decode($request->getContent(), true);

        // A removal requires data
        if (empty($content)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        // remove the images with given id
        // - optionally also physically remove the image from the server
        return new JsonResponse(null, 204);
    }

    /**
     * Clear the images data.
     *
     * @Route("/api/images/all", name="api_timelineitems_clear", methods={"DELETE"})
     */
    public function clearTimelineItems(TimelineItemRepository $itemRepo): Response
    {
        // empty images list
        // reset images state
        return new JsonResponse(null, 204);
    }
}
