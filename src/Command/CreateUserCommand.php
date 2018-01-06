<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use App\Entity\User;

class CreateUserCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'app:user:create';

    /**
     * @var UserRepository 
     */
    protected $userRepository;

    /**
     * @var RoleRepository 
     */
    protected $roleRepository;

    /**
     * @var UserPasswordEncoderInterface 
     */
    protected $userPasswordEncoder;

    /**
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
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

        $output->writeln(array(
            'Username: '.$user->getUsername(),
            'Encoded password: '.$user->getPassword(),
        ));

        if ($input->getOption('is-admin')) {
            $role = $this->roleRepository->loadRoleByName('ROLE_ADMIN');
            $user->setRoles(array($role));
        }

        $this->userRepository->createUser($user);

        $io->success('New user created.');
    }
}
