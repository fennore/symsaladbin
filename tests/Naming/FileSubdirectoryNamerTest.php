<?php

namespace App\Tests\Naming;

use App\Naming\FileSubdirectoryNamer;
use App\Reader\DirectoryReader;
use App\Tests\DummyFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class FileSubdirectoryNamerTest extends KernelTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
    }

    public function fileDataProvider()
    {
        yield ['0123456789.jpg', 'image/jpeg', getenv(DirectoryReader::SUBDIRECTORYNAME_IMAGES).'/'];
        yield ['0123456789.png', 'image/png', getenv(DirectoryReader::SUBDIRECTORYNAME_IMAGES).'/'];
        yield ['0123456789.gif', 'image/gif', '/'];
        yield ['0123456789.doc', 'application/msword', getenv(DirectoryReader::SUBDIRECTORYNAME_STORIES).'/'];
        yield ['0123456789.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', getenv(DirectoryReader::SUBDIRECTORYNAME_STORIES).'/'];
        yield ['0123456789.odt', 'application/vnd.oasis.opendocument.text', getenv(DirectoryReader::SUBDIRECTORYNAME_STORIES).'/'];
        yield ['0123456789.gpx', 'application/xml', getenv(DirectoryReader::SUBDIRECTORYNAME_GPX).'/'];
    }

    /**
     * @dataProvider fileDataProvider
     */
    public function testDirectoryName(string $fileName, string $mimeType, string $expectedDirectory): void
    {
        self::bootKernel();

        $entity = new DummyFile();
        $entity->setMimeType($mimeType);
        $mapping = $this->createMock(PropertyMapping::class);
        $namer = new FileSubdirectoryNamer(static::$container->get(DirectoryReader::class));
        $this->assertSame($expectedDirectory, $namer->directoryName($entity, $mapping));
    }
}
