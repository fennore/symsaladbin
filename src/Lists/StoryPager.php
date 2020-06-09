<?php

namespace App\Lists;

use App\Repository\StoryRepository;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

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
 *     name = "last",
 *     href = @Hateoas\Route(
 *         "api_stories",
 *         parameters = {
 *              "offset" = "expr(object.getLast())",
 *              "limit" = "expr(object.getLimit())"
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "stories",
 *     embedded = "expr(object.showPage())"
 * )
 */
class StoryPager extends AbstractPager
{
    /**
     * @Serializer\Exclude
     */
    private bool $showDisabled;

    /**
     * @Serializer\Exclude
     */
    private StoryRepository $storyRepo;

    public function __construct(
        int $offset,
        int $limit,
        bool $showDisabled,
        StoryRepository $storyRepo)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->showDisabled = $showDisabled;
        $this->storyRepo = $storyRepo;
        $this->total = $this->storyRepo->countAll($this->showDisabled);
    }

    /**
     * @return Story[]
     */
    public function showPage(): array
    {
        $list = [];
        foreach (
            $this->storyRepo->getRange(
                $this->offset,
                $this->limit,
                $this->showDisabled
            ) as $row) {
            $list[] = $row[0];
        }

        return $list;
    }
}
