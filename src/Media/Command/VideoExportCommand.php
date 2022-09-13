<?php

namespace App\Media\Command;

use App\Media\Entity\Video;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoMediaManager;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class VideoExportCommand extends Command
{
    protected static $defaultName = 'video:export';

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
            ->addArgument('videoId', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getArgument('videoId')) {
            /** @var Video $video */
            $video = $this->videoProvider->get(Uuid::fromString($input->getArgument('videoId')));

            $this->videoMediaManager->export($video);

            return 0;
        }

        /** @var Video $video */
        foreach ($this->videoProvider->findAll() as $video) {
            try {
                $this->videoMediaManager->export($video);
            } catch (Exception $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }

        return 0;
    }
}
