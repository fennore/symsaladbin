<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StoryRepository;
use App\Repository\TimelineItemRepository;

class PublicPageController extends AbstractSmartController
{
    /**
     * Matches /.
     *
     * @Route("/", name="intro", defaults={"_format": "html"})
     */
    public function intro()
    {
        return $this->smartRender('default/intro.html.twig');
    }

    /**
     * Matches /about.
     *
     * @Route("/about", name="about", defaults={"_format": "html"})
     */
    public function about()
    {
        return $this->smartRender('default/about.html.twig');
    }

    /**
     * Matches /timeline.
     *
     * @Route("/timeline", name="timeline", defaults={"_format": "html"})
     */
    public function viewTimeline(TimelineItemRepository $timelineItemRepository)
    {
        return $this->smartRender('default/timeline.html.twig', [
            'timeline' => $timelineItemRepository->getTimelineItems(),
        ]);
    }

    /**
     * Matches /story/xxxx.
     *
     * @Route("/timeline/{path}", name="timeline_item", defaults={"_format": "html"})
     */
    public function viewTimelineItem(TimelineItemRepository $timelineItemRepository, $path = null)
    {
        return $this->smartRender('default/timeline-item.html.twig', [
            'timelineitem' => $timelineItemRepository->getTimelineItemFromPath($path),
        ]);
    }

    /**
     * Matches /story.
     *
     * @Route("/story", name="story_list", defaults={"_format": "html"})
     */
    public function viewStoryList(StoryRepository $storyRepository)
    {
        return $this->smartRender('default/story-list.html.twig', [
            'storylist' => $storyRepository->getStories(),
        ]);
    }

    /**
     * Matches /story/xxxx.
     *
     * @Route("/story/{path}", name="story", defaults={"_format": "html"})
     */
    public function viewStory(StoryRepository $storyRepository, $path = null)
    {
        return $this->smartRender('default/story.html.twig', [
            'story' => $storyRepository->getStoryFromPath($path),
        ]);
    }
}
