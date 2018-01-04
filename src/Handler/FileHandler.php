<?php

namespace App\Handler;

use App\Repository\FileRepository;
use App\Entity\File;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Handles files in the files directory
 */
class FileHandler
{

    /**
     * @var FileRepository 
     */
    private $fileRepository;

    /**
     * @var Finder 
     */
    private $fileFinder;

    /**
     * @var string Files directory
     */
    private $filesDirectory;

    /**
     * 
     * @param \App\Handler\Container $container
     * @param FileRepository $fileRepository
     * @param Finder $fileFinder
     */
    public function __construct(Container $container, FileRepository $fileRepository, Finder $fileFinder)
    {
        $this->filesDirectory = $container->getParameter('app.files.directory') ?? '';
        $this->fileRepository = $fileRepository;
        $this->fileFinder = $fileFinder;
    }

    public function syncSourceWithFileEntity()
    {
        // Select all files from db in array format
        $dbFileSources = array_column($this->fileRepository->findAll(), 'id', 'source');
        // Or use iterator
        // $dbFiles = $this->fileRepository->getFiles()
        // Fetch all files from files directory
        $dirFiles = $this->fileFinder->files()
            ->followLinks() // Follow symbolic links!
            ->in($this->filesDirectory);
        // Write new files to database
        array_walk($dirFiles, function(SplFileInfo $splFileInfo) use ($dbFileSources) {
            $relativeSource = $splFileInfo->getRelativePathname();
            $id = $dbFileSources[str_replace('\\', '/', $relativeSource)] ?? false;
            if ($id !== false) {
                // Skip already recorded files
                unset($dbFileSources[$id]);
                return;
            }
            $file = new File(new BaseFile($relativeSource));
            $this->fileRepository->createFile($file);
        });
        // Remove orphan records
        foreach ($dbFileSources as $source => $id) {
            $file = $this->fileRepository->find($id);
            $this->fileRepository->deleteFile($file);
        }
        //
        return array_values($dbFileSources);
    }

    public function getFilesDirectory(): string
    {
        return $this->filesDirectory;
    }

}
