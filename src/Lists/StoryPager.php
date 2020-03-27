<?php

namespace App\Lists;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use App\Repository\StoryRepository;

/**
 * Story pager wrapper.
 *
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_stories",
 *         parameters = { 
 *              "offset" = "expr(object.getOffset())",
 *              "limit" = "expr(object.getLimit())" 
 *         },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "next",
 *     href = @Hateoas\Route(
 *         "api_stories",
 *         parameters = { 
 *              "offset" = "expr(object.getNext())",
 *              "limit" = "expr(object.getLimit())" 
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "previous",
 *     href = @Hateoas\Route(
 *         "api_stories",
 *         parameters = { 
 *              "offset" = "expr(object.getPrevious())",
 *              "limit" = "expr(object.getLimit())" 
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "first",
 *     href = @Hateoas\Route(
 *         "api_stories",
 *         parameters = { 
 *              "offset" = "0",
 *              "limit" = "expr(object.getLimit())" 
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "stories",
 *     embedded = "expr(object.getStories())"
 * )
 */
class StoryPager
{
    /**
     * @Serializer\Exclude
     * 
     * @var bool 
     */
    private bool $showDisabled;

    /**
     * @Serializer\Exclude
     * 
     * @var int
     */
    private int $offset;

    /**
     * @Serializer\Exclude
     * 
     * @var int 
     */
    private int $limit;

    /**
     * @Serializer\Exclude
     * 
     * @var StoryRepository 
     */
    private StoryRepository $storyRepo;

    /**
     * @param int $offset
     * @param int $limit
     * @param StoryRepository $storyRepo
     */
    public function __construct(
        int $offset, 
        int $limit, 
        StoryRepository $storyRepo)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->storyRepo = $storyRepo;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param bool $showDisabled
     * @return void
     */
    public function setShowDisabled(bool $showDisabled): self
    {
        $this->showDisabled = $showDisabled;

        return $this;
    }

    /**
     * @return Story[]
     */
    public function getStories(): array
    {
        $list = [];
        foreach (
            $this->storyRepo->getStories(
                $this->offset, 
                $this->limit, 
                $this->showDisabled
            ) as $row)
        {
            $list[] = $row[0];
        }

        return $list;
    }

    /**
     * @return int
     */
    public function getNext(): int
    {
        return min(
            floor($this->offset/$this->limit) * $this->limit + $this->limit, 
            floor($this->storyRepo->countStories($this->showDisabled) / $this->limit) * $this->limit
        );
    }

    /**
     * @return int
     */
    public function getPrevious(): int
    {
        return max(floor($this->offset/$this->limit) * $this->limit - $this->limit, 0);
    }
}
