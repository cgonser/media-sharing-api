<?php

namespace App\Notification\Controller\UserNotificationChannel;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Service\UserNotificationChannelManager;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Notification Channel')]
#[Route(path: '/users/current/notification_channels')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationChannelProvider $provider,
        private readonly UserNotificationChannelManager $manager,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{userNotificationChannelId}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        #[OA\PathParameter] string $userNotificationChannelId,
    ): Response {
        $userNotificationChannel = $this->provider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($userNotificationChannelId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::DELETE, $userNotificationChannel);

        $this->manager->delete($userNotificationChannel);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
