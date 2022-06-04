<?php

namespace App\Notification\Controller\UserNotificationChannel;

use App\Core\Response\ApiJsonResponse;
use App\Notification\Dto\UserNotificationChannelDto;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Request\NotificationSearchRequest;
use App\Notification\Request\UserNotificationChannelSearchRequest;
use App\Notification\ResponseMapper\UserNotificationChannelResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Notification Channel')]
#[Route(path: '/users/current/notification_channels')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationChannelResponseMapper $responseMapper,
        private readonly UserNotificationChannelProvider $provider,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: UserNotificationChannelSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        headers: [
            new OA\Header(header: "X-Total-Count", schema: new OA\Schema(type: "int")),
        ],
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: new Model(type: UserNotificationChannelDto::class))
        )
    )]
    #[Route(name: 'user_notification_channels_find', methods: 'GET')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function find(NotificationSearchRequest $searchRequest): Response
    {
        $searchRequest->userId = $this->getUser()->getId()->toString();

        $results = $this->provider->search($searchRequest);
        $count = $this->provider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->responseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
