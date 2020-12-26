<?php

namespace App\Tests\Naming;

use App\Naming\FileSubdirectoryNamer;
use App\Reader\DirectoryReader;
use App\Tests\DummyFile;
use PHPUnit\Framework\TestCase;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class FileSubdirectoryNamerTest extends TestCase
{
    private const SUBDIRECTORYNAME_IMAGES = 'images';
    private const SUBDIRECTORYNAME_STORIES = 'stories';
    private const SUBDIRECTORYNAME_GPX = 'gpx';

    public function fileDataProvider()
    {
        yield ['0123456789.jpg', 'image/jpeg', self::SUBDIRECTORYNAME_IMAGES.'/'];
        yield ['0123456789.png', 'image/png', self::SUBDIRECTORYNAME_IMAGES.'/'];
        yield ['0123456789.gif', 'image/gif', '/'];
        yield ['0123456789.doc', 'application/msword', self::SUBDIRECTORYNAME_STORIES.'/'];
        yield ['0123456789.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', self::SUBDIRECTORYNAME_STORIES.'/'];
        yield ['0123456789.odt', 'application/vnd.oasis.opendocument.text', self::SUBDIRECTORYNAME_STORIES.'/'];
        yield ['0123456789.gpx', 'application/xml', self::SUBDIRECTORYNAME_GPX.'/'];
    }

    /**
     * @dataProvider fileDataProvider
     */
    public function testDirectoryName(string $fileName, string $mimeType, string $expectedDirectory): void
    {
        $directoryReader = $this->createMock(DirectoryReader::class);
        $directoryReader
            ->expects($this->any())
            ->method('getImagesDirectory')
            ->willReturn(self::SUBDIRECTORYNAME_IMAGES);
        $directoryReader
            ->expects($this->any())
            ->method('getGpxDirectory')
            ->willReturn(self::SUBDIRECTORYNAME_GPX);
        $directoryReader
            ->expects($this->any())
            ->method('getStoriesDirectory')
            ->willReturn(self::SUBDIRECTORYNAME_STORIES);

        $entity = new DummyFile();
        $entity->setMimeType($mimeType);
        $mapping = $this->createMock(PropertyMapping::class);
        $namer = new FileSubdirectoryNamer($directoryReader);
        $this->assertSame($expectedDirectory, $namer->directoryName($entity, $mapping));
    }
}
