<?php

namespace App\Tests\Handler;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Handler\FileHandler;
use App\Reader\DirectoryReader;
use App\Repository\FileRepository;

class FileHandlerTest extends KernelTestCase
{
    public function testSyncSourceWithFileEntity(): void
    {
        self::bootKernel();

        $directoryReader = new DirectoryReader(self::$kernel->getContainer());
        $fileRepository = $this->createMock(FileRepository::class);
        $handler = new FileHandler($directoryReader, $fileRepository);
        $this->assertDirectoryExists($directoryReader->getFilesDirectory());
        $handler->syncSourceWithFileEntity();
    }
}
