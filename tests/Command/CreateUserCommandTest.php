<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\CreateUserCommand;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel(); 
        $application = new Application($kernel);
        // Create mocks
        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);
        $userPasswordEncoder = $this->createMock(UserPasswordEncoder::class);
        $application->add(new CreateUserCommand($userRepository, $roleRepository, $userPasswordEncoder));

        $command = $application->find('app:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),

            // pass arguments to the helper
            'username' => 'testadmin',
            'password' => 'mytravel',
            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
            '--is-admin' => true
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('Username: testadmin', $output);

    }
}
