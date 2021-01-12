<?php

namespace App\Importer;

use App\Entity\Item\Story;
use App\Reader\SimpleDocumentReader;
use App\Repository\{FileRepository,StoryRepository};

final class DocumentImporter
{

    private $storyMapper;
    
    public function __construct(
        private FileRepository $fileRepo, 
        private StoryRepository $storyRepo, 
        private StoryFromFile $storyFromFile
    )
    {}

    public function importDocuments(): void
    {
        $this->buildMapper();
        
        foreach ($this->fileRepository->getFiles(Story::MIMEMATCH) as $file) {
            $story = $this->storyFromFile->create($file);
            $mapperId = $story->getPath();
            if (
                isset($this->storyMapper[$mapperId]) &&
                $story->getUpdated() <= $this->storyMapper[$mapperId]->getUpdated()
            ) {
                continue;
            }
            if (isset($this->storyMapper[$mapperId])) {
                $this->storyRepository->updateStory(
                    $this->storyMapper[$mapperId]
                        ->setContent($story->getContent())
                );
            } else {
                $this->storyRepository->createStory($story);
            }
        }
    }

    private function buildMapper() {
        $this->storyMapper = [];
        
        foreach ($this->storyRepository->getAll() as $story) {
            $this->storyMapper[$story->getPath()] = $story;
        }
    }
}
