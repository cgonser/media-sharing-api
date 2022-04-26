<?php

namespace App\User\Command;

use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserFixFollowersCounterCommand extends Command
{
    protected static $defaultName = 'user:fix-followers-counter';

    public function __construct(
        private UserProvider $userProvider,
        private UserManager $userManager,
        private UserFollowProvider $userFollowProvider,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fixes followers counter for one or all users')
            ->addArgument('userId', InputArgument::OPTIONAL, 'User ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null !== $input->getArgument('userId')) {
            $this->fixUser($this->userProvider->get(Uuid::fromString($input->getArgument('userId'))));

            return Command::SUCCESS;
        }

        $users = $this->userProvider->findAll();

        foreach ($users as $user) {
            $this->fixUser($user);
        }

        return Command::SUCCESS;
    }

    protected function fixUser(User $user): void
    {
        $user->setFollowersCount($this->userFollowProvider->countByFollowingId($user->getId()));
        $user->setFollowingCount($this->userFollowProvider->countByFollowerId($user->getId()));

        $this->userManager->save($user);
    }
}
