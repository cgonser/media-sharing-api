<?php

namespace App\Notification\Controller\UserNotificationChannel;

use App\Notification\Request\UserNotificationChannelRequest;
use App\Notification\Service\UserNotificationChannelRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'User / Notification Channel')]
#[Route(path: '/users/current/notification_channels')]
class CreateController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationChannelRequestManager $userNotificationChannelRequestManager,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserNotificationChannelRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'user_notification_channel_post', methods: ['POST'])]
    #[ParamConverter(
        data: 'userNotificationChannelRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        UserNotificationChannelRequest $userNotificationChannelRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $userNotificationChannelRequest->userId = $this->getUser()->getId()->toString();

        $this->userNotificationChannelRequestManager->createOrUpdateFromRequest($userNotificationChannelRequest);

        return new Response(null, 204);
    }
}
