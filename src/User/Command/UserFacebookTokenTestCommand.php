<?php

namespace App\User\Command;

use App\Core\Service\FacebookApiClientFactory;
use App\User\Entity\UserIntegration;
use App\User\Provider\UserIntegrationProvider;
use App\User\Provider\UserProvider;
use App\User\Service\UserFacebookIntegrationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserFacebookTokenTestCommand extends Command
{
    protected static $defaultName = 'user:facebook-token-test';

    public function __construct(
        private UserFacebookIntegrationManager $userFacebookIntegrationManager,
        private UserProvider $userProvider,
        private UserIntegrationProvider $userIntegrationProvider,
        private FacebookApiClientFactory $facebookApiClientFactory,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('tests a facebook token')
            ->addArgument('email', InputArgument::OPTIONAL, 'User e-mail');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userProvider->findOneByEmail($input->getArgument('email'));

        if (null === $user) {
            $output->writeln('User not found');

            return Command::FAILURE;
        }

        $userIntegrations = $this->userIntegrationProvider->findAll();
        /** @var UserIntegration $userIntegration */
        foreach ($userIntegrations as $userIntegration) {
            if (null === $userIntegration->getAccessToken()) {
                continue;
            }
            echo PHP_EOL.$userIntegration->getUser()->getEmail().PHP_EOL;
            $facebookApi = $this->facebookApiClientFactory->createInstance($userIntegration->getAccessToken());

            try {
                $this->userFacebookIntegrationManager->validateToken($userIntegration, $facebookApi);
            } catch (\Exception $e) {
                echo $e->getMessage().PHP_EOL;
            }
        }

        return Command::SUCCESS;
    }
}
