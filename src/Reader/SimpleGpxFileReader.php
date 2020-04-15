<?php

namespace App\Reader;

use App\Entity\Coordinate;
use App\Entity\File;
use App\Entity\Location;
use SimpleXMLIterator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple Reader of Gpx files.
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

    public function __construct(ContainerInterface $container)
    {
        $this->filesDirectory = $container->getParameter('app.files.directory');
    }

    /**
     * Generator method.
     * Yields new Location entities.
     *
     * @param File $file  the GPX file to read from
     * @param int  $stage which stage GPX locations belong to
     */
    public function getGpxAsLocations(File $file, int $stage)
    {
        $gpx = new SimpleXMLIterator($file->getSource(), LIBXML_NOCDATA, true);
        $gpx->rewind();
        // Validate gpx file
        if (!$gpx->valid() || !$gpx->hasChildren()) {
            return;
        }
        $i = 0;

        // Add all GPX data as Location
        foreach ($gpx->children() as $coordinate) {
            if ('wpt' !== $coordinate->getName()) {
                return;
            }
            yield new Location(
                new Coordinate((float) $coordinate->attributes()->lat, (float) $coordinate->attributes()->lon), $coordinate->name ?? 'noname', $stage, $i++
            );
        }
    }
}
