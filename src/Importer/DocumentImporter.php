<?php

namespace App\Importer;

use App\Repository\FileRepository;
use App\Repository\StoryRepository;
use App\Reader\SimpleDocumentReader;
use App\Entity\Item\Story;

class DocumentImporter
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var StoryRepository
     */
    private $storyRepository;

    /**
     * @var SimpleDocumentReader
     */
    private $simpleDocumentReader;

    public function __construct(FileRepository $fileRepo, StoryRepository $storyRepo, SimpleDocumentReader $simpleDocReader)
    {
        $this->fileRepository = $fileRepo;
        $this->storyRepository = $storyRepo;
        $this->simpleDocumentReader = $simpleDocReader;
    }

    /**
     * Import stories from documents.
     */
    public function importDocuments(): void
    {
        $fileList = $this->fileRepository->getFiles(Story::MIMEMATCH);
        $storyList = $this->storyRepository->getStories();
        $storyCheckList = [];
        foreach ($storyList as $row) {
            $storyCheckList[$row[0]->getPath()] = $row[0];
        }
        foreach ($fileList as $row) {
            $story = $this->simpleDocumentReader->getDocumentAsStory($row[0]);
            if (
                isset($storyCheckList[$story->getPath()]) &&
                $story->getUpdated() > $storyCheckList[$story->getPath()]->getUpdated()
            ) {
                $this->storyRepository->updateStory(
                    $storyCheckList[$story->getPath()]
                        ->setContent($story->getContent())
                );
            } elseif (isset($storyCheckList[$story->getPath()])) {
                continue; // skip
            } else {
                $this->storyRepository->createStory($story);
            }
        }
    }
}
