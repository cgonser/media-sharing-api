<?php

namespace App\Notification\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Notification\Notification\CustomPushNotification;
use App\Notification\Request\CustomPushNotificationRequest;
use App\Notification\Service\Notifier;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Notifications')]
#[Route(path: '/users/current/notifications')]
class CreateController extends AbstractController
{
    public function __construct(
        private readonly Notifier $notifier,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: CustomPushNotificationRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'user_notification_post', methods: ['POST'])]
    #[ParamConverter(
        data: 'request',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        CustomPushNotificationRequest $request
    ): Response {
        $notification = new CustomPushNotification();
        $notification->subject($request->subject);
        $notification->content($request->contents);

        $this->notifier->sendRaw($notification, $this->getUser());

        return new ApiJsonResponse(
            Response::HTTP_NO_CONTENT
        );
    }
}
