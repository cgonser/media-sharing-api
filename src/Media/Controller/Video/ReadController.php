<?php

namespace App\Media\Controller\Video;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoDto;
use App\Media\Entity\Video;
use App\Media\Enumeration\VideoStatus;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoSearchRequest;
use App\Media\ResponseMapper\VideoResponseMapper;
use App\User\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video')]
#[Route(path: '/videos')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly VideoProvider $videoProvider,
        private readonly VideoResponseMapper $videoResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: VideoSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: VideoDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'videos_find', methods: ['GET'])]
    public function find(VideoSearchRequest $searchRequest, Request $request): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, Video::class);

        if ('current' === $searchRequest->userId) {
            $searchRequest->userId = $this->getUser()->getId()->toString();
        }

        if ($this->getUser()->getId()->toString() !== $searchRequest->userId) {
            $searchRequest->followerId = $this->getUser()->getId()->toString();
            $searchRequest->status = VideoStatus::PUBLISHED;
        }

        if (!empty($searchRequest->userIdExclusions)) {
            $searchRequest->userIdExclusions = str_replace(
                'current',
                $this->getUser()->getId()->toString(),
                $searchRequest->userIdExclusions,
            );
        }

        $count = $this->videoProvider->count($searchRequest);
        $results = $this->videoProvider->search($searchRequest);

        $userId = $this->getUser()->getId();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $userId->toString() === $searchRequest->userId
                ? $this->videoResponseMapper->mapMultiple($results, $userId)
                : $this->videoResponseMapper->mapMultiplePublic($results, $userId),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: VideoDto::class))
     )]
    #[Route(path: '/{videoId}', name: 'videos_get_one', methods: ['GET'])]
    public function getOne(#[OA\PathParameter] string $videoId): Response
    {
        /** @var Video $video */
        $video = $this->videoProvider->get(Uuid::fromString($videoId));

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $userId = $this->getUser()->getId();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $video->getUserId()->equals($userId)
                ? $this->videoResponseMapper->map($video, $userId)
                : $this->videoResponseMapper->mapPublic($video, $userId)
        );
    }
}
