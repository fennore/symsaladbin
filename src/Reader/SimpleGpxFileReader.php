<?php

namespace App\Reader;

use SimpleXMLIterator;
use App\Entity\File;
use App\Entity\Coordinate;
use App\Entity\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple Reader of Gpx files
 * 
 * GPX Schema Documentation: http://www.topografix.com/gpx/1/1/
 * 
 * Note: if ever there would be the need for more complex gpx file processing,
 * there exists https://github.com/Sibyx/phpGPX
 * Like... support for tracks and routes instead of only waypoints?
 */
class SimpleGpxFileReader
{

    private $filesDirectory;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->filesDirectory = $container->getParameter('app.files.directory');
    }

    /**
     * Generator method.
     * Yields new Location entities.
     * @param File $file The GPX file to read from.
     * @param int $stage Which stage GPX locations belong to.
     */
    public function saveGpxAsLocations(File $file, int $stage)
    {
        $gpx = new SimpleXMLIterator($file->getSource(), LIBXML_NOCDATA, true);
        // Validate gpx file
        $gpx->rewind();
        
        if (!$gpx->valid() || !$gpx->hasChildren()) {
            return;
        }
        $i = 0;
        
        // Add all GPX data as Location
        foreach ($gpx->children() as $coordinate) {
            if($coordinate->getName() !== 'wpt') {
                return;
            }
            yield new Location(
                new Coordinate((float) $coordinate->attributes()->lat, (float) $coordinate->attributes()->lon), $coordinate->name ?? "noname", $stage, $i++
            );
        }
    }

}
