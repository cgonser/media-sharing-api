<?php

namespace App\Notification\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Notification\Dto\NotificationDto;
use App\Notification\Request\NotificationSearchRequest;
use App\Notification\ResponseMapper\NotificationResponseMapper;
use App\Notification\Provider\NotificationProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Notifications')]
#[Route(path: '/users/current/notifications')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly NotificationResponseMapper $notificationResponseMapper,
        private readonly NotificationProvider $notificationProvider,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: NotificationSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        headers: [
            new OA\Header(header: "X-Total-Count", schema: new OA\Schema(type: "int")),
        ],
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: NotificationDto::class)))
    )]
    #[Route(name: 'notification_find', methods: 'GET')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function find(NotificationSearchRequest $searchRequest): Response
    {
        $searchRequest->userId = $this->getUser()->getId()->toString();

        $results = $this->notificationProvider->search($searchRequest);
        $count = $this->notificationProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->notificationResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
