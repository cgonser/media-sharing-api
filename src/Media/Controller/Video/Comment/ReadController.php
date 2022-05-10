<?php

namespace App\Media\Controller\Video\Comment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoCommentDto;
use App\Media\Provider\VideoCommentProvider;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoCommentSearchRequest;
use App\Media\ResponseMapper\VideoCommentResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video / Comments')]
#[Route(path: '/videos/{videoId}/comments')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly VideoProvider $videoProvider,
        private readonly VideoCommentProvider $videoCommentProvider,
        private readonly VideoCommentResponseMapper $videoCommentResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: VideoCommentSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: VideoCommentDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'videos_comments_find', methods: ['GET'])]
    public function find(#[OA\PathParameter] string $videoId, VideoCommentSearchRequest $searchRequest): Response
    {
        $video = $this->videoProvider->get(Uuid::fromString($videoId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $results = $this->videoCommentProvider->search($searchRequest);
        $count = $this->videoCommentProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->videoCommentResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
