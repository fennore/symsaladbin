<?php

namespace App\Lists;

use App\Repository\TimelineItemRepository;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * TimelineItem pager wrapper.
 *
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_timelineitems",
 *         parameters = {
 *              "offset" = "expr(object.getOffset())",
 *              "limit" = "expr(object.getLimit())"
 *         },
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "next",
 *     href = @Hateoas\Route(
 *         "api_timelineitems",
 *         parameters = {
 *              "offset" = "expr(object.getNext())",
 *              "limit" = "expr(object.getLimit())"
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "previous",
 *     href = @Hateoas\Route(
 *         "api_timelineitems",
 *         parameters = {
 *              "offset" = "expr(object.getPrevious())",
 *              "limit" = "expr(object.getLimit())"
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "first",
 *     href = @Hateoas\Route(
 *         "api_timelineitems",
 *         parameters = {
 *              "offset" = "0",
 *              "limit" = "expr(object.getLimit())"
 *         }
 *     )
 * )
 * @Hateoas\Relation(
 *     name = "timelineitems",
 *     embedded = "expr(object.showPage())"
 * )
 */
class TimelineItemPager extends AbstractPager
{
    /**
     * @Serializer\Exclude
     */
    private bool $showDisabled;

    /**
     * @Serializer\Exclude
     */
    private TimelineItemRepository $itemRepo;

    public function __construct(
        int $offset,
        int $limit,
        bool $showDisabled,
        TimelineItemRepository $itemRepo)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->showDisabled = $showDisabled;
        $this->itemRepo = $itemRepo;
        $this->total = $this->itemRepo->countAll($this->showDisabled);
    }

    /**
     * @return TimelineItem[]
     */
    public function showPage(): array
    {
        $list = [];
        foreach (
            $this->itemRepo->getRange(
                $this->offset,
                $this->limit,
                $this->showDisabled
            ) as $row) {
            $list[] = $row[0];
        }

        return $list;
    }
}
