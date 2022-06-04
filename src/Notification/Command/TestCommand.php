<?php

namespace App\Notification\Command;

use App\Media\Notification\MomentPublishedNotification;
use App\Media\Provider\MomentProvider;
use App\Notification\Service\Notifier;
use App\User\Provider\UserProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'notification:test';

    public function __construct(
        private readonly Notifier $notifier,
        private readonly UserProvider $userProvider,
        private readonly MomentProvider $momentProvider,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'username')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userProvider->findOneByUsername($input->getArgument('username'));
        $moment = $this->momentProvider->findOneBy([]);

        $this->notifier->send(new MomentPublishedNotification($moment), $user);

        return 0;
    }
}
