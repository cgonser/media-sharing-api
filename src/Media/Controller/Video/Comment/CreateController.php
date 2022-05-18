<?php

namespace App\Media\Controller\Video\Comment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoCommentDto;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoCommentRequest;
use App\Media\ResponseMapper\VideoCommentResponseMapper;
use App\Media\Service\VideoCommentManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Video / Comments')]
#[Route(path: '/videos/{videoId}/comments')]
class CreateController extends AbstractController
{
    public function __construct(
        private readonly VideoProvider $videoProvider,
        private readonly VideoCommentManager $videoCommentManager,
        private readonly VideoCommentResponseMapper $videoCommentResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: VideoCommentRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: VideoCommentDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(name: 'videos_comments_create', methods: ['POST'])]
    #[ParamConverter(
        data: 'videoCommentRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body')
    ]
    public function create(
        #[OA\PathParameter] string $videoId,
        VideoCommentRequest $videoCommentRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $video = $this->videoProvider->get(Uuid::fromString($videoId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $videoComment = $this->videoCommentManager->create($video, $this->getUser(), $videoCommentRequest->comment);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->videoCommentResponseMapper->map($videoComment)
        );
    }
}
