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
    public function importDocuments()
    {
        $fileList = $this->fileRepository->getFiles(Story::MIMEMATCH);
        foreach ($fileList as $row) {
            $doc = $row[0];
            $story = $this->simpleDocumentReader->getDocumentAsStory($doc);
            $this->storyRepository->createStory($story);
        }
    }
}
