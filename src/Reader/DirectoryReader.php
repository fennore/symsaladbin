<?php

namespace App\Reader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Get all files in the files directory.
     */
    public function getAllFiles(): Finder
    {
        return Finder::create()
            ->files()
            ->followLinks() // Follow symbolic links!
            ->in($this->getFilesDirectory());
    }

    /**
     * @param bool $getRelative Set to true if you want the path relative to the project root
     */
    public function getFilesDirectory(bool $getRelative = false): string
    {
        $projectDirectory = $this->params->get('kernel.project_dir').'/';
        $filesDirectoryName = getenv(self::DIRECTORYNAME_FILES) ?? 'var/files';
        $baseDirectory = $getRelative ? '' : $projectDirectory;

        return $baseDirectory.$filesDirectoryName;
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getGpxDirectory(bool $getRelative = false): string
    {
        $filesDirectory = $this->getFilesDirectory().'/';
        $gpxDirectoryName = getenv(self::SUBDIRECTORYNAME_GPX) ?? 'gpx';
        $baseDirectory = $getRelative ? '' : $filesDirectory;

        return $baseDirectory.$gpxDirectoryName;
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getStoriesDirectory(bool $getRelative = false): string
    {
        $filesDirectory = $this->getFilesDirectory().'/';
        $storiesDirectoryName = getenv(self::SUBDIRECTORYNAME_STORIES) ?? 'stories';
        $baseDirectory = $getRelative ? '' : $filesDirectory;

        return $baseDirectory.$storiesDirectoryName;
    }

    /**
     * @param bool $getRelative Set to true if you want to get the path relative to the files directory
     */
    public function getImagesDirectory(bool $getRelative = false): string
    {
        $filesDirectory = $this->getFilesDirectory().'/';
        $imagesDirectoryName = getenv(self::SUBDIRECTORYNAME_IMAGES) ?? 'images';
        $baseDirectory = $getRelative ? '' : $filesDirectory;

        return $baseDirectory.$imagesDirectoryName;
    }
}
