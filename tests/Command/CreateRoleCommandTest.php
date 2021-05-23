<?php

namespace App\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use App\Command\CreateRoleCommand;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManager;

final class CreateRoleCommandTest extends KernelTestCase
{

    private MockObject $roleRepository;

    public function setUp(): void
    {
        $this->roleRepository = $this->createMock(RoleRepository::class);
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->roleRepository);
        parent::tearDown();
    }

    public function testExecute()
    {
        $em = $this->createMock(EntityManager::class)
            ->expects($this->once())
            ->method('persist');
        $this->roleRepository
            ->expects($this->any())
            ->method('getEntityManager')
            ->willReturnReference($em);

        $commandTester = new CommandTester($this->initiateCommand());
        $commandTester->execute([
            'name' => 'ROLE_TEST',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('[OK]', $output);
        $this->assertContains('[ROLE_TEST] created', $output);
    }

    private function initiateCommand(): Command
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CreateRoleCommand($this->roleRepository));

        return $application->find('app:role:create');
    }
}
