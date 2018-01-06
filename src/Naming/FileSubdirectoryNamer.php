<?php

namespace App\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * File directory namer for Vich Uploader bundle.
 *
 * @see https://github.com/dustin10/VichUploaderBundle/blob/master/Resources/doc/directory_namer/howto/create_a_custom_directory_namer.md
 */
class FileSubdirectoryNamer implements DirectoryNamerInterface
{
    private $noSubDir = '';
    private $subDirImages;
    private $subDirGpx;
    private $subDirStories;

    public function __construct(ContainerInterface $container)
    {
        $this->subDirImages = $container->getParameter('app.files.subdir.images');
        $this->subDirGpx = $container->getParameter('app.files.subdir.gpx');
        $this->subDirStories = $container->getParameter('app.files.subdir.stories');
    }

    /**
     * {@inheritdoc}
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return $this->determineSubDirectoryForMimeType($object->getMimeType()).'/';
    }

    private function determineSubDirectoryForMimeType(string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/png':
                return $this->subDirImages;
            case 'application/xml':
                return $this->subDirGpx;
            case 'application/vnd.oasis.opendocument.text':
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return $this->subDirStories;
            default:
                return $this->noSubDir;
        }
    }
}
