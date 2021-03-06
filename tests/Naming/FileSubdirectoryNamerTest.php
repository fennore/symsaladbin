<?php

namespace App\Tests\Naming;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Naming\FileSubdirectoryNamer;
use App\Reader\DirectoryReader;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use App\Tests\DummyFile;

class FileSubdirectoryNamerTest extends KernelTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
    }

    public function fileDataProvider()
    {
        yield ['0123456789.jpg', 'image/jpeg', static::$kernel->getContainer()->getParameter('app.files.subdir.images').'/'];
        yield ['0123456789.png', 'image/png', static::$kernel->getContainer()->getParameter('app.files.subdir.images').'/'];
        yield ['0123456789.gif', 'image/gif', '/'];
        yield ['0123456789.doc', 'application/msword', static::$kernel->getContainer()->getParameter('app.files.subdir.stories').'/'];
        yield ['0123456789.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', static::$kernel->getContainer()->getParameter('app.files.subdir.stories').'/'];
        yield ['0123456789.odt', 'application/vnd.oasis.opendocument.text', static::$kernel->getContainer()->getParameter('app.files.subdir.stories').'/'];
        yield ['0123456789.gpx', 'application/xml', static::$kernel->getContainer()->getParameter('app.files.subdir.gpx').'/'];
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
        $reader = new DirectoryReader(static::$kernel->getContainer());
        $namer = new FileSubdirectoryNamer($reader);
        $this->assertSame($expectedDirectory, $namer->directoryName($entity, $mapping));
    }
}
