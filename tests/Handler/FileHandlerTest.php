<?php

namespace App\Tests\Handler;

use App\Handler\FileHandler;
use App\Reader\DirectoryReader;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileHandlerTest extends KernelTestCase
{
    public function testSyncSourceWithFileEntity(): void
    {
        static::bootKernel();

        $handler = new FileHandler(
            static::$container->get(DirectoryReader::class),
            static::$container->get(FileRepository::class)
        );
        $this->assertIsIterable($handler->syncSourceWithFileEntity());
    }
}
