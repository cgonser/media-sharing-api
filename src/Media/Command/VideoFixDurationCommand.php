<?php

namespace App\Media\Command;

use App\Media\Entity\Video;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoManager;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoFixDurationCommand extends Command
{
    protected static $defaultName = 'video:fix-duration';

    public function __construct(
        protected readonly VideoManager $videoManager,
        protected readonly VideoProvider $videoProvider,
        protected readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('videoId', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('videoId')) {
            /** @var Video $video */
            $video = $this->videoProvider->get(Uuid::fromString($input->getArgument('videoId')));

            $this->videoManager->defineVideoDuration($video);
            $this->videoManager->update($video);

            return 0;
        }

        /** @var Video $video */
        foreach ($this->videoProvider->findAll() as $video) {
            try {
                $this->videoManager->defineVideoDuration($video);
                $this->videoManager->update($video);
            } catch (Exception $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }

        return 0;
    }
}
