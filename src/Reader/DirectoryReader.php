<?php

namespace App\Reader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Returns App directory information or content using the Finder Component.
 */
class DirectoryReader
{
    /**
     * Files directory parameter name.
     */
    const DIRECTORYNAME_FILES = 'APP_FILES_DIRECTORY';

    /**
     * Gpx files subdirectory parameter name.
     */
    const SUBDIRECTORYNAME_GPX = 'APP_FILES_SUBDIR_GPX';

    /**
     * Story files subdirectory parameter name.
     */
    const SUBDIRECTORYNAME_STORIES = 'APP_FILES_SUBDIR_STORIES';

    /**
     * Image files subdirectory parameter name.
     */
    const SUBDIRECTORYNAME_IMAGES = 'APP_FILES_SUBDIR_IMAGES';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get all files in the files directory.
     */
    public function getAllFiles(): Finder
    {
        return Finder::create()->files()
            ->followLinks() // Follow symbolic links!
            ->in($this->getFilesDirectory());
    }

    /**
     * @param bool $getRelative Set to true if you want the path relative to the project root
     */
    public function getFilesDirectory(bool $getRelative = false): string
    {
        return
            ($getRelative ? '' : $this->container->getParameter('kernel.project_dir').'/').
            ($_ENV[self::DIRECTORYNAME_FILES] ?? 'var/files');
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getGpxDirectory(bool $getRelative = false): string
    {
        return ($getRelative ? '' : $this->getFilesDirectory().'/').($_ENV[self::SUBDIRECTORYNAME_GPX] ?? 'gpx');
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getStoriesDirectory(bool $getRelative = false): string
    {
        return ($getRelative ? '' : $this->getFilesDirectory().'/').($_ENV[self::SUBDIRECTORYNAME_STORIES] ?? 'stories');
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getImagesDirectory(bool $getRelative = false): string
    {
        return ($getRelative ? '' : $this->getFilesDirectory().'/').($_ENV[self::SUBDIRECTORYNAME_IMAGES] ?? 'images');
    }
}
