<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'app:user:create';

    public function __construct(
        private UserRepository $userRepository, 
        private RoleRepository $roleRepository, 
        private UserPasswordEncoderInterface $userPasswordEncoder
    )
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a user')
            ->setHelp('This command creates a user in the database')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addOption('is-admin', null, InputOption::VALUE_NONE, 'Is admin user', null)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $user = new User($username, null);
        $password = $this->userPasswordEncoder->encodePassword($user, $input->getArgument('password'));
        $user->setPassword($password);

        $output->writeln([
            "Username: {$user->getUsername()}",
            "Encrypted password: {$user->getPassword()}",
        ]);

        if ($input->getOption('is-admin')) {
            $role = $this->roleRepository->loadRoleByName('ROLE_ADMIN');
            $user->setRoles([$role]);
        }

        $roles = implode($user->getRoles());
        $output->writeln([
            "Roles: [{$roles}]" 
        ]);

        $this->userRepository->createUser($user);

        $io->success('New user created.');

        return 0;
    }
}
