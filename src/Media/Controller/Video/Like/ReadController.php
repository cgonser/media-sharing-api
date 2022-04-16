<?php

namespace App\Media\Controller\Video\Like;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoLikeDto;
use App\Media\Provider\VideoLikeProvider;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoLikeSearchRequest;
use App\Media\ResponseMapper\VideoLikeResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video / Likes')]
#[Route(path: '/videos/{videoId}/likes')]
class ReadController extends AbstractController
{
    public function __construct(
        private VideoProvider $videoProvider,
        private VideoLikeProvider $videoLikeProvider,
        private VideoLikeResponseMapper $videoLikeResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: VideoLikeSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: VideoLikeDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'videos_likes_find', methods: ['GET'])]
    public function find(#[OA\PathParameter] string $videoId, VideoLikeSearchRequest $searchRequest): Response
    {
        $video = $this->videoProvider->get(Uuid::fromString($videoId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $results = $this->videoLikeProvider->search($searchRequest);
        $count = $this->videoLikeProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->videoLikeResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
