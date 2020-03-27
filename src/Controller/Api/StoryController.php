<?php

namespace App\Controller\Api;

use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\StoryRepository;
use App\Lists\StoryPager;

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
    public function getStories(StoryRepository $storyRepo, SerializerInterface $serializer, int $offset = 0, int $limit = 1)
    {
        $pager = new StoryPager($offset, $limit, $storyRepo);
        $pager->setShowDisabled($this->isGranted('ROLE_ADMIN'));
        $json = $serializer->serialize($pager, 'json');

        return JsonResponse::fromJsonString($json);
    }

    /**
     * Update the stories using given identifier.
     *
     * @Route("/api/stories", name="api_stories_update", methods={"PUT"})
     */
    public function updateStories()
    {
        
    }

    /**
     * Delete the stories using given identifier.
     *
     * @Route("/api/stories", name="api_stories_delete", methods={"DELETE"})
     */
    public function deleteStories()
    {
        
    }

    /**
     * Clear stories.
     *
     * @Route("/api/stories/all", name="api_stories_clear", methods={"DELETE"})
     */
    public function clearStories()
    {
        // empty stories list
        // reset stories state
        return JsonResponse::create(null, 204);
    }
}
