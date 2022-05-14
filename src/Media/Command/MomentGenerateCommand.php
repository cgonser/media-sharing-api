<?php

namespace App\Media\Command;

use App\Media\Entity\Location;
use App\Media\Entity\Moment;
use App\Media\Enumeration\MomentStatus;
use App\Media\Enumeration\Mood;
use App\Media\Provider\LocationProvider;
use App\Media\Service\MomentManager;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use DateTime;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MomentGenerateCommand extends Command
{
    protected static $defaultName = 'moment:generate';

    public function __construct(
        protected readonly MomentManager $momentManager,
        protected readonly UserProvider $userProvider,
        protected readonly LocationProvider $locationProvider,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL, 'How many moments?', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userProvider->findAll();
        $moods = Mood::cases();

        $locations = $this->locationProvider->findAll();
        shuffle($locations);

        for ($i=0; $i < intval($input->getOption('count')); $i++) {
            $this->createMoment(
                $users[array_rand($users)],
                $locations[$i],
                $moods[array_rand($moods)]
            );
        }

        return 0;
    }

    protected function createMoment(User $user, Location $location, Mood $mood): void
    {
        $moment = new Moment();
        $moment->setUser($user);
        $moment->setLocation($location);
        $moment->setMood($mood);
        $moment->setDuration(rand(100, 400) / 100);
        $moment->setStatus(MomentStatus::PUBLISHED);
        $moment->setRecordedAt(new DateTime());
        $moment->setPublishedAt(new DateTime());

        $this->momentManager->create($moment);
    }
}
