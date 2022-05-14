<?php

namespace App\Notification\Command;

use App\Media\Entity\Moment;
use App\Media\Notification\MomentPublishedNotification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class TestCommand extends Command
{
    protected static $defaultName = 'notification:test';

    public function __construct(
        private readonly NotifierInterface $notifier,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $notification = new MomentPublishedNotification(new Moment);

        $this->notifier->send(
            $notification,
            new Recipient(
                'carlos+itinair@gonser.eu',
            )
        );

        return 0;
    }
}
