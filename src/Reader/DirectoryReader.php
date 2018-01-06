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
     * Files directory parameter name
     */
    const DIRECTORYNAME_FILES = 'app.files.directory';

    /**
     * Gpx files subdirectory parameter name
     */
    const SUBDIRECTORYNAME_GPX = 'app.files.subdir.gpx';

    /**
     * Story files subdirectory parameter name
     */
    const SUBDIRECTORYNAME_STORIES = 'app.files.subdir.stories';

    /**
     * Image files subdirectory parameter name
     */
    const SUBDIRECTORYNAME_IMAGES = 'app.files.subdir.images';

    /**
     * @var ContainerInterface 
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get all files in the files directory.
     *
     * @return Finder
     */
    public function getAllFiles(): Finder
    {
        return Finder::create()->files()
            ->followLinks() // Follow symbolic links!
            ->in($this->getFilesDirectory());
    }

    /**
     * @param bool $getRelative Set to true if you want the path relative to the project root
     * @return string
     */
    public function getFilesDirectory(bool $getRelative = false): string
    {
        return ($getRelative?'':$this->container->getParameter('kernel.project_dir').'/').($this->container->getParameter(self::DIRECTORYNAME_FILES) ?? 'var/files');
    }
    
    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     * @return string
     */
    public function getGpxDirectory(bool $getRelative = false): string
    {
        return ($getRelative?'':$this->getFilesDirectory().'/').($this->container->getParameter(self::SUBDIRECTORYNAME_GPX) ?? 'gpx');
    }
    
    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     * @return string
     */
    public function getStoriesDirectory(bool $getRelative = false): string
    {
        return ($getRelative?'':$this->getFilesDirectory().'/').($this->container->getParameter(self::SUBDIRECTORYNAME_STORIES) ?? 'stories');
    }
    
    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     * @return string
     */
    public function getImagesDirectory(bool $getRelative = false): string
    {
        return ($getRelative?'':$this->getFilesDirectory().'/').($this->container->getParameter(self::SUBDIRECTORYNAME_IMAGES) ?? 'images');
    }
}
