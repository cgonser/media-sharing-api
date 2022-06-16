<?php

namespace App\User\Command;

use App\User\Provider\UserProvider;
use App\User\Service\UserPasswordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserPasswordChangeCommand extends Command
{
    protected static $defaultName = 'user:password-change';

    public function __construct(
        private readonly UserPasswordManager $userPasswordManager,
        private readonly UserProvider $userProvider
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('changes an user password')
            ->addArgument('email', InputArgument::REQUIRED, 'User e-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'new password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userProvider->findOneByEmail($input->getArgument('email'));

        if (null === $user) {
            $output->writeln('User not found');

            return Command::FAILURE;
        }

        $this->userPasswordManager->doChangePassword($user, $input->getArgument('password'));

        return Command::SUCCESS;
    }
}
