<?php

namespace App\User\Controller\Integration;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Provider\UserIntegrationProvider;
use App\User\Service\UserIntegrationManager;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/users/{userId}/integrations')]
class UserIntegrationDeleteController extends AbstractController
{
    public function __construct(
        private UserIntegrationProvider $userIntegrationProvider,
        private UserIntegrationManager $userIntegrationManager,
    ) {
    }

    /**
     * @OA\Tag(name="User / Integrations")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=401, description="Invalid credentials")
     * @OA\Response(response=404, description="Integration not found")
     */
    #[Route(path: '/{platform}', name: 'user_integration_delete', methods: ['DELETE'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function deleteByUserAndPlatform(User $user, string $platform): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userIntegration = $this->userIntegrationProvider->getByUserAndPlatform($user->getId(), $platform);

        $this->userIntegrationManager->delete($userIntegration);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
