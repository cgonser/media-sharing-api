<?php

namespace App\Notification\Controller\UserNotificationChannel;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Notification\Dto\UserNotificationChannelDto;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Request\UserNotificationChannelRequest;
use App\Notification\ResponseMapper\UserNotificationChannelResponseMapper;
use App\Notification\Service\UserNotificationChannelRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'User / Notification Channel')]
#[Route(path: '/users/current/notification_channels')]
class UpdateController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationChannelProvider $provider,
        private readonly UserNotificationChannelRequestManager $requestManager,
        private readonly UserNotificationChannelResponseMapper $responseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserNotificationChannelRequest::class)))]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserNotificationChannelDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{userNotificationChannelId}', name: 'user_notification_channels_update', methods: ['PATCH', 'PUT'])]
    #[ParamConverter(
        data: 'userNotificationChannelRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        #[OA\PathParameter] string $userNotificationChannelId,
        UserNotificationChannelRequest $userNotificationChannelRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $userNotificationChannel = $this->provider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($userNotificationChannelId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $userNotificationChannel);

        $this->requestManager->updateFromRequest($userNotificationChannel, $userNotificationChannelRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->responseMapper->map($userNotificationChannel)
        );
    }
}
