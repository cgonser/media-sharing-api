<?php

namespace App\Media\Controller\Video;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoDto;
use App\Media\Enumeration\VideoStatus;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoRequest;
use App\Media\Request\VideoStatusRequest;
use App\Media\ResponseMapper\VideoResponseMapper;
use App\Media\Service\VideoManager;
use App\Media\Service\VideoRequestManager;
use App\User\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Video')]
#[Route(path: '/videos')]
class UpdateController extends AbstractController
{
    public function __construct(
        private readonly VideoProvider $videoProvider,
        private readonly VideoManager $videoManager,
        private readonly VideoRequestManager $videoRequestManager,
        private readonly VideoResponseMapper $videoResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: VideoRequest::class)))]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: VideoDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{videoId}', name: 'videos_update', methods: ['PATCH', 'PUT'])]
    #[ParamConverter(
        data: 'videoRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        #[OA\PathParameter] string $videoId,
        VideoRequest $videoRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $video = $this->videoProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $video);

        $this->videoRequestManager->updateFromRequest($video, $videoRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->videoResponseMapper->map($video));
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{videoId}/publication', name: 'videos_publish', methods: ['PUT'])]
    public function publish(
        #[OA\PathParameter] string $videoId,
    ): Response {
        $video = $this->videoProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $video);

        $this->videoManager->publish($video);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{videoId}/publication', name: 'videos_unpublish', methods: ['DELETE'])]
    public function unpublish(
        #[OA\PathParameter] string $videoId,
    ): Response {
        $video = $this->videoProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $video);

        $this->videoManager->unpublish($video);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
