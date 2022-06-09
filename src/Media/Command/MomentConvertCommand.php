<?php

namespace App\Media\Command;

use App\Media\Entity\Moment;
use App\Media\Provider\MomentProvider;
use App\Media\Service\MomentMediaManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MomentConvertCommand extends Command
{
    protected static $defaultName = 'moment:convert';

    public function __construct(
        protected readonly MomentMediaManager $momentMediaManager,
        protected readonly MomentProvider $momentProvider,
        protected readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('momentId', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Moment $moment */
        $moment = $this->momentProvider->get(Uuid::fromString($input->getArgument('momentId')));

        $this->momentMediaManager->convert($moment);

        return 0;
    }
}
