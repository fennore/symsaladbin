<?php

namespace App\Importer;

use App\Entity\Item\Story;
use App\Reader\SimpleDocumentReader;
use App\Repository\FileRepository;
use App\Repository\StoryRepository;

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
        $storyList = $this->storyRepository->getAllStories();
        $storyCheckList = [];
        foreach ($storyList as $row) {
            $storyCheckList[$row[0]->getPath()] = $row[0];
        }
        foreach ($fileList as $file) {
            $story = $this->simpleDocumentReader->getDocumentAsStory($file);
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
