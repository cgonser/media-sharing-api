<?php

namespace App\Media\Controller\Video;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\VideoDto;
use App\Media\Entity\Video;
use App\Media\Request\VideoRequest;
use App\Media\ResponseMapper\VideoResponseMapper;
use App\Media\Service\VideoRequestManager;
use App\User\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Video')]
#[Route(path: '/videos')]
class CreateController extends AbstractController
{
    public function __construct(
        private VideoRequestManager $videoManager,
        private VideoResponseMapper $videoResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: VideoRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: VideoDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'videos_create', methods: ['POST'])]
    #[ParamConverter(
        data: 'videoRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body')
    ]
    public function create(
        VideoRequest $videoRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::CREATE, Video::class);

        if (!$videoRequest->has('userId') || !$this->getUser()->hasRole(User::ROLE_ADMIN)) {
            $videoRequest->userId = $this->getUser()->getId()->toString();
        }

        $video = $this->videoManager->createFromRequest($videoRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->videoResponseMapper->map($video));
    }
}
