<?php

namespace App\Media\Command;

use App\Media\Entity\Location;
use App\Media\Service\LocationManager;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LocationGenerateCommand extends Command
{
    protected static $defaultName = 'location:generate';

    public function __construct(
        private readonly LocationManager $locationManager,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('count', 'c', InputOption::VALUE_OPTIONAL, 'How many points?', 100)
            ->addArgument('minLong', InputArgument::REQUIRED, 'min Long')
            ->addArgument('maxLong', InputArgument::REQUIRED, 'max Long')
            ->addArgument('minLat', InputArgument::REQUIRED, 'min Lat')
            ->addArgument('maxLat', InputArgument::REQUIRED, 'min Lat')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $minLat = (float) $input->getArgument('minLat');
        $minLong = (float) $input->getArgument('minLong');
        $maxLat = (float) $input->getArgument('maxLat');
        $maxLong = (float) $input->getArgument('maxLong');

        for ($i=0; $i < intval($input->getOption('count')); $i++) {
            $lat = rand($minLat * 1000000, $maxLat * 1000000) / 1000000;
            $long = rand($minLong * 1000000, $maxLong * 1000000) / 1000000;

            $this->createLocation($long, $lat);
        }

        return 0;
    }

    private function createLocation(float $long, float $lat)
    {
        $location = new Location();
        $location->setCoordinates(new Point($long, $lat));

        $this->locationManager->createOrRetrieveLocation($location);
    }
}
