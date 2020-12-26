<?php

namespace App\Tests\Handler;

use App\Entity\File;
use App\Handler\FileHandler;
use App\Reader\DirectoryReader;
use App\Repository\FileRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class FileHandlerTest extends TestCase
{
    private $recordedFiles = [];
    private $removedFiles = [];

    public function testSyncSourceWithFileEntity(): void
    {
        $directoryReader = $this->createDirectoryReaderMock();
        $fileRepository = $this->createFileRepositoryMock();

        $fileRepository->expects($this->exactly(7))
            ->method('createFile')
            ->with($this->isInstanceOf(File::class))
            ->with($this->callback(fn ($file) => $this->pushRecordedFile()));

        $fileRepository->expects($this->exactly(1))
            ->method('deleteFile')
            ->with($this->isInstanceOf(File::class))
            ->with($this->callback(fn ($file) => $this->pushRemovedFile()));

        $handler = new FileHandler(
            $directoryReader,
            $fileRepository
        );

        $handler->syncSourceWithFileEntity();

        $this->assertEquals([
            'TestFile_1.odt',
            'TestFile_2.odt',
            'TestFile_3.odt',
            'TestFile_4.odt',
            'TestFile_5.odt',
            'TestFile_6.odt',
            'TestFile_7.odt',
        ], $this->recordedFiles);

        $this->assertEquals([
            'TestOrphanFile_1.odt',
        ], $this->removedFiles);
    }

    private function createDirectoryReaderMock(): MockObject
    {
        return $this->createMock(DirectoryReader::class)
            ->expects($this->once())
            ->method('getAllFiles')
            ->willReturnCallback(fn () => $this->getDirectoryFileList());
    }

    private function createFileRepositoryMock(): MockObject
    {
        $fileRepository = $this->createMock(FileRepository::class)
            ->expects($this->once())
            ->method('getFiles')
            ->willReturnCallback(fn () => $this->getDatabaseFileList());
    }

    private function getDirectoryFileList()
    {
        yield new SplFileInfo('TestFile_1.odt');
        yield new SplFileInfo('TestRecordedFile_1.odt');
        yield new SplFileInfo('TestFile_2.doc');
        yield new SplFileInfo('TestFile_3.jpg');
        yield new SplFileInfo('TestRecordedFile_2.jpg');
        yield new SplFileInfo('TestFile_4.vcs');
        yield new SplFileInfo('TestFile_5.gif');
        yield new SplFileInfo('TestFile_6.xml');
        yield new SplFileInfo('TestRecordedFile_3.xml');
        yield new SplFileInfo('TestFile_7.pdf');
    }

    private function getDatabaseFileList()
    {
        yield new SplFileInfo('TestOrphanFile_1.odt');
        yield new SplFileInfo('TestRecordedFile_1.odt');
        yield new SplFileInfo('TestRecordedFile_2.jpg');
        yield new SplFileInfo('TestRecordedFile_3.xml');
    }

    private function pushRecordedFile(File $file)
    {
        $this->recordedFiles[] = $file->getBaseName();
    }

    private function pushRemovedFile(File $file)
    {
        $this->removedFiles[] = $file->getBaseName();
    }
}
