<?php

namespace App\Controller\Api;

use App\Lists\StoryPager;
use App\Repository\StoryRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoryController extends AbstractController
{
    /**
     * Returns a list of stories starting from given offset and as many as given length.
     *
     * @Route(
     *      "/api/stories/{offset}/{limit}",
     *      name="api_stories", methods={"GET", "HEAD"},
     *      requirements={"offset"="\d+","length"="\d+"})
     */
    public function getStories(
        StoryRepository $storyRepo,
        SerializerInterface $serializer,
        int $offset = 0,
        int $limit = 1): Response
    {
        $pager = new StoryPager(
            $offset,
            $limit,
            $this->isGranted('ROLE_ADMIN'),
            $storyRepo
        );
        $json = $serializer->serialize($pager, 'json');

        return JsonResponse::fromJsonString($json);
    }

    /**
     * Create the new stories.
     *
     * @Route("/api/stories", name="api_stories_create", methods={"POST"})
     */
    public function createStories(
        Request $request,
        StoryRepository $storyRepository,
        SerializerInterface $serializer)
    {
        $stories = $serializer->deserialize($request->getContent(), 'array<App\Entity\Item\Story>', 'json');
        foreach ($stories as $story) {
            $storyRepository->createStory($story);
        }

        return JsonResponse::create(null, 201);
    }

    /**
     * Update the stories using given identifier.
     *
     * @Route("/api/stories", name="api_stories_update", methods={"PUT"})
     */
    public function updateStories(
        Request $request,
        StoryRepository $storyRepository,
        SerializerInterface $serializer)
    {
        $stories = $serializer->deserialize($request->getContent(), 'array<App\Entity\Item\Story>', 'json');

        if (is_array($stories) && count($stories) > 0) {
            $storyRepository->updateStories($stories);
        }

        return JsonResponse::create([], 204);
    }

    /**
     * Delete the stories using given identifier.
     *
     * @Route("/api/stories", name="api_stories_delete", methods={"DELETE"})
     */
    public function deleteStories(
        Request $request,
        StoryRepository $storyRepository,
        SerializerInterface $serializer)
    {
        return JsonResponse::create(null, 204);
    }

    /**
     * Clear stories.
     *
     * @Route("/api/stories/all", name="api_stories_clear", methods={"DELETE"})
     */
    public function clearStories(StoryRepository $storyRepository)
    {
        $storyRepository->truncateTable();

        return JsonResponse::create(null, 204);
    }
}
