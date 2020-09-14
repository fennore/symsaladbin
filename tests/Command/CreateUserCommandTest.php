<?php

namespace App\Tests\Command;

use App\Command\CreateUserCommand;
use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class CreateUserCommandTest extends KernelTestCase
{
    private RoleRepository $roleRepository;

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
        $commandTester = new CommandTester($this->initiateCommand());
        $commandTester->execute([
            'username' => 'testuser',
            'password' => 'mytravel',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('[OK]', $output);
        $this->assertContains('Username: testuser', $output);
        $this->assertContains('Encrypted password: ', $output);
        $this->assertContains('Roles: []', $output);
    }

    public function testExecuteAsAdmin()
    {
        $role = new Role('ROLE_ADMIN');
        $this->roleRepository->expects($this->once())
            ->method('loadRoleByName')
            ->with('ROLE_ADMIN')
            ->willReturn($role);
        $commandTester = new CommandTester($this->initiateCommand());
        $commandTester->execute([
            'username' => 'testadmin',
            'password' => 'mytravel',
            '--is-admin' => true,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('[OK]', $output);
        $this->assertContains('Username: testadmin', $output);
        $this->assertContains('Encrypted password: ', $output);
        $this->assertContains('Roles: [ROLE_ADMIN]', $output);
    }

    private function initiateCommand(): Command
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $userRepository = $this->createMock(UserRepository::class);
        $userPasswordEncoder = $this->createMock(UserPasswordEncoder::class);
        $application->add(new CreateUserCommand($userRepository, $this->roleRepository, $userPasswordEncoder));

        return $application->find('app:user:create');
    }
}
