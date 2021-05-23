<?php

namespace App\Command;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateRoleCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'app:role:create';

    public function __construct(
        private RoleRepository $roleRepository
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
            ->setDescription('Add a new role')
            ->addArgument('name', InputArgument::OPTIONAL, 'Unique name for the role')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $roleName = $input->getArgument('name');
        $role = new Role($roleName);
        $this->roleRepository->createRole($role);

        $io->success("[{$role}] created.");

        return 0;
    }
}
