<?php

namespace App\User\Service;

use App\Core\Validation\EntityValidator;
use App\User\Entity\UserIntegration;
use App\User\Repository\UserIntegrationRepository;

class UserIntegrationManager
{
    public function __construct(
        private UserIntegrationRepository $userIntegrationRepository,
        private EntityValidator $validator
    ) {
    }

    public function save(UserIntegration $userIntegration): void
    {
        $this->validator->validate($userIntegration);

        $this->userIntegrationRepository->save($userIntegration);
    }

    public function delete(?UserIntegration $userIntegration): void
    {
        $this->userIntegrationRepository->delete($userIntegration);
    }
}
