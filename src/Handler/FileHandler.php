<?php

namespace App\Handler;

use App\Entity\File;
use App\Reader\DirectoryReader;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

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

    public function __construct(DirectoryReader $directoryReader, FileRepository $fileRepository)
    {
        $this->directoryReader = $directoryReader;
        $this->fileRepository = $fileRepository;
    }

    public function syncSourceWithFileEntity()
    {
        // Create a file source hash list using iterator
        $dbFileSources = [];
        foreach ($this->fileRepository->getFiles() as $row) {
            $file = $row[0];
            $dbFileSources[$file->getSource()] = $file->getId();
        }
        // Fetch all files from files directory
        $dirFiles = $this->directoryReader->getAllFiles();
        // Write new files to database
        foreach ($dirFiles as $splFileInfo) {
            $pathName = str_replace('\\', '/', $splFileInfo->getPathname());
            $id = $dbFileSources[$pathName] ?? false;
            if (false !== $id) {
                // Skip already recorded files
                unset($dbFileSources[$pathName]);

                continue;
            }
            $file = new File(new BaseFile($pathName));
            $this->fileRepository->createFile($file);
        }

        // Remove orphan records
        foreach ($dbFileSources as $source => $id) {
            $file = $this->fileRepository->find($id);
            $this->fileRepository->deleteFile($file);
        }

        return $this->fileRepository->getFiles();
    }
}
