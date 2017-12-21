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
use App\Entity\User;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:user:create';
    protected $userRepository;
    protected $userPasswordEncoder;
    
    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userRepository = $userRepository;
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
            'Encoded password: '.$user->getPassword()
        ));
        
        if($input->getOption('is-admin')) {
            $user->setRoles(array('ROLE_ADMIN'));
        }
        
        $this->userRepository->createUser($user);
 
        $io->success('New user created.');
    }
}
