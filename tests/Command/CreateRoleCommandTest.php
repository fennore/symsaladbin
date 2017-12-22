<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Repository\RoleRepository;
use App\Command\CreateRoleCommand;

class CreateRoleCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel(); 
        $application = new Application($kernel);
        // Create mocks
        $roleRepository = $this->createMock(RoleRepository::class);
        $application->add(new CreateRoleCommand($roleRepository));

        $command = $application->find('app:role:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),

            // pass arguments to the helper
            'name' => 'ROLE_ADMIN',
            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('New role created', $output);
    }
}
