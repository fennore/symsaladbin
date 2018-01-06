<?php

namespace App\Handler;

use App\Repository\FileRepository;
use App\Entity\File;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use App\Reader\DirectoryReader;

/**
 * Handles files in the files directory.
 */
class FileHandler
{
    /**
     * @var DirectoryReader
     */
    private $directoryReader;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @param DirectoryReader $directoryReader
     * @param FileRepository  $fileRepository
     */
    public function __construct(DirectoryReader $directoryReader, FileRepository $fileRepository)
    {
        $this->directoryReader = $directoryReader;
        $this->fileRepository = $fileRepository;
    }

    public function syncSourceWithFileEntity()
    {
        // Select all files from db in array format
        $dbFileSources = array_column($this->fileRepository->findAll(), 'id', 'source');
        // Or use iterator
        // $dbFiles = $this->fileRepository->getFiles()
        // Fetch all files from files directory
        $dirFiles = $this->directoryReader->getAllFiles();

        // Write new files to database
        foreach ($dirFiles as $splFileInfo) {
            $id = $dbFileSources[str_replace('\\', '/', $splFileInfo->getRelativePathname())] ?? false;

            if (false !== $id) {
                // Skip already recorded files
                unset($dbFileSources[$id]);
                
                continue;
            }
            $file = new File(new BaseFile($splFileInfo->getPathname()));
            $this->fileRepository->createFile($file);
        }
        // Remove orphan records
        foreach ($dbFileSources as $source => $id) {
            $file = $this->fileRepository->find($id);
            $this->fileRepository->deleteFile($file);
        }

        return array_values($dbFileSources);
    }
}
