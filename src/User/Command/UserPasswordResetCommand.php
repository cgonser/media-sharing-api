<?php

namespace App\User\Command;

use App\User\Provider\UserProvider;
use App\User\Service\UserPasswordManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserPasswordResetCommand extends Command
{
    protected static $defaultName = 'user:password-reset';

    public function __construct(
        private UserPasswordManager $userPasswordManager,
        private UserProvider $userProvider
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('resets an user password')
            ->addArgument('email', InputArgument::OPTIONAL, 'User e-mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userProvider->findOneByEmail($input->getArgument('email'));

        if (null === $user) {
            $output->writeln('User not found');

            return Command::FAILURE;
        }

        $this->userPasswordManager->startPasswordReset($user);

        return Command::SUCCESS;
    }
}
