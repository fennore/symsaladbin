<?php

namespace App\Tests\Command;

use App\Command\CreateRoleCommand;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CreateRoleCommandTest extends KernelTestCase
{
    public function testExecute()
    {
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

        $roleRepository = $this->createMock(RoleRepository::class);
        $application->add(new CreateRoleCommand($roleRepository));

        return $application->find('app:role:create');
    }
}
