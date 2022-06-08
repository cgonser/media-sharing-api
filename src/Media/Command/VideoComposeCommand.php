<?php

namespace App\Media\Command;

use App\Media\Entity\Video;
use App\Media\Message\VideoCreatedEvent;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoMediaManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoComposeCommand extends Command
{
    protected static $defaultName = 'video:compose';

    public function __construct(
        protected readonly VideoMediaManager $videoMediaManager,
        protected readonly VideoProvider $videoProvider,
        protected readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('videoId', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Video $video */
        $video = $this->videoProvider->get(Uuid::fromString($input->getArgument('videoId')));

        $this->messageBus->dispatch(new VideoCreatedEvent($video->getId(), $video->getUserId()));

//        $this->videoMediaManager->compose($video);

        return 0;
    }
}
