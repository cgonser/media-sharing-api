<?php

namespace App\Core\Doctrine\Migrations;

use App\User\Service\UserManager;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MigrationFactory implements \Doctrine\Migrations\Version\MigrationFactory
{
    public function __construct(
        private Connection $connection,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private UserManager $userService
    ) {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = new $migrationClassName(
            $this->connection,
            $this->logger
        );

        if ($migration instanceof CoreMigration) {
            $migration->addService(EntityManagerInterface::class, $this->entityManager);
            $migration->addService(UserManager::class, $this->userService);
        }

        return $migration;
    }
}
