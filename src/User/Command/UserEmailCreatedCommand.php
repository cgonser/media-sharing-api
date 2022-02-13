<?php

namespace App\User\Command;

use App\User\Provider\UserProvider;
use App\User\Service\UserEmailManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserEmailCreatedCommand extends Command
{
    protected static $defaultName = 'user:email-created';

    public function __construct(
        private UserEmailManager $userEmailManager,
        private UserProvider $userProvider
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Sends user created e-mail')
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

        $this->userEmailManager->sendCreatedEmail($user);

        return Command::SUCCESS;
    }
}
