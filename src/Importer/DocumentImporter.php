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
        foreach ($fileList as $row) {
            $story = $this->simpleDocumentReader->getDocumentAsStory($row[0]);
            $this->storyRepository->createStory($story);
        }
    }
}
