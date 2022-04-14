<?php

namespace App\Media\Controller\Video;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoDto;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoRequest;
use App\Media\ResponseMapper\VideoResponseMapper;
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
        private VideoProvider $videoProvider,
        private VideoRequestManager $videoManager,
        private VideoResponseMapper $videoResponseMapper,
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
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter(
        data: 'videoRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        #[OA\PathParameter] string $videoId,
        VideoRequest $videoRequest,
        User $user,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $video = $this->videoProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $video);

        $this->videoManager->updateFromRequest($video, $videoRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->videoResponseMapper->map($video));
    }
}
