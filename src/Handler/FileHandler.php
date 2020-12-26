<?php

namespace App\Handler;

use App\Entity\File;
use App\Reader\DirectoryReader;
use App\Repository\FileRepository;
use SplFileInfo;
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

    public function syncSourceWithFileEntity(): void
    {
        $recordedFiles = $this->getRecordedFilesMap();

        $this->recordFiles($this->directoryReader->getAllFiles(), $recordedFiles);

        $this->removeOrphanRecords();
    }

    /**
     * @param SplFileInfo[] $directoryFiles
     * @param int[]         $recordedFiles  Mapping file source => ID
     */
    private function recordFiles($directoryFiles, $recordedFiles): void
    {
        foreach ($directoryFiles as $splFileInfo) {
            $pathName = str_replace('\\', '/', $splFileInfo->getPathname());
            $id = $recordedFiles[$pathName] ?? false;
            if (false !== $id) {
                // Skip already recorded files
                unset($recordedFiles[$pathName]);

                continue;
            }
            $file = new File(new BaseFile($pathName));
            $this->fileRepository->createFile($file);
        }
    }

    /**
     * Remove all files recorded that are no longer present in the directory.
     */
    private function removeOrphanRecords(): void
    {
        foreach ($recordedFiles as $source => $id) {
            $file = $this->fileRepository->find($id);
            $this->fileRepository->deleteFile($file);
        }
    }

    /**
     * @return iterable [file source => ID]
     */
    private function getRecordedFilesMap(): iterable
    {
        foreach ($this->fileRepository->getFiles() as $file) {
            yield [$file->getSource() => $file->getId()];
        }
    }
}
