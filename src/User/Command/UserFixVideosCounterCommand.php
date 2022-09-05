<?php

namespace App\User\Command;

use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoSearchRequest;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\Service\UserManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserFixVideosCounterCommand extends Command
{
    protected static $defaultName = 'user:fix-videos-counter';

    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserManager $userManager,
        private readonly VideoProvider $videoProvider,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fixes videos counter for one or all users')
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
        $searchRequest = new VideoSearchRequest();
        $searchRequest->userId = $user->getId()->toString();

        $user->setVideoCount($this->videoProvider->count($searchRequest));

        $this->userManager->save($user);
    }
}
