<?php

namespace App\Importer;

use App\Entity\Item\TimelineItem;
use App\Reader\SimpleImageReader;
use App\Repository\FileRepository;
use App\Repository\TimelineItemRepository;

class ImageImporter
{
    /** @var FileRepository */
    private $fileRepository;
    /** @var TimelineItemRepository */
    private $timelineItemRepository;
    /** @var SimpleImageReader */
    private $imageReader;

    public function __construct(FileRepository $fileRepository, TimelineItemRepository $timelineItemRepository, SimpleImageReader $imageReader)
    {
        $this->fileRepository = $fileRepository;
        $this->timelineItemRepository = $timelineItemRepository;
        $this->imageReader = $imageReader;
    }

    public function importImages(): void
    {
        $files = $this->fileRepository->getFiles(TimelineItem::MIMEMATCH);
        $itemList = $this->timelineItemRepository->getTimelineItems();
        $itemCheckList = [];
        foreach ($itemList as $item) {
            $itemCheckList[$item->getPath()] = $item;
        }
        foreach ($files as $file) {
            $timelineItem = $this->imageReader->getImageAsTimelineItem($file);
            if (
                isset($itemCheckList[$timelineItem->getPath()]) &&
                $timelineItem->getUpdated() > $itemCheckList[$timelineItem->getPath()]->getUpdated()
            ) {
                $this->timelineItemRepository->updateTimelineItem(
                    $itemCheckList[$timelineItem->getPath()]
                        ->setContent($timelineItem->getContent())
                );
            } elseif (isset($itemCheckList[$timelineItem->getPath()])) {
                continue; // skip
            } else {
                $this->timelineItemRepository->createTimelineItem($timelineItem);
            }
        }
    }
}
