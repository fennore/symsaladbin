<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Role;
use App\Repository\RoleRepository;

class CreateRoleCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected static $defaultName = 'app:role:create';

    /**
     * @var RoleRepository 
     */
    protected $roleRepository;

    /**
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Add a new role')
            ->addArgument('name', InputArgument::OPTIONAL, 'Unique name for the role')
        ;
    }

    /**
     * {@inheritDoc}
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $roleName = $input->getArgument('name');
        $role = new Role($roleName);
        $this->roleRepository->createRole($role);

        $io->success('New role created.');
    }
}
