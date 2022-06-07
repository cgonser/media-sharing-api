<?php

namespace App\Notification\Command;

use App\Notification\Notification\CustomPushNotification;
use App\Notification\Service\Notifier;
use App\User\Provider\UserProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'notification:test';

    public function __construct(
        private readonly Notifier $notifier,
        private readonly UserProvider $userProvider,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'username')
            ->addArgument('contents', InputArgument::REQUIRED, 'contents')
            ->addOption('subject', 's', InputOption::VALUE_OPTIONAL, 'subject', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userProvider->findOneByUsername($input->getArgument('username'));

        $notification = new CustomPushNotification();
        $notification->subject($input->getOption('subject'));
        $notification->content($input->getArgument('contents'));

        $this->notifier->sendRaw($notification, $user);

        return 0;
    }
}
