<?php

namespace App\Media\Controller\Video\MediaItem;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MediaItemDto;
use App\Media\Provider\VideoProvider;
use App\Media\Request\VideoMediaItemRequest;
use App\Media\ResponseMapper\MediaItemResponseMapper;
use App\Media\Service\VideoMediaItemManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Video / Media Item')]
#[Route(path: '/videos/{videoId}/media_items')]
class CreateController extends AbstractController
{
    public function __construct(
        private VideoProvider $videoProvider,
        private VideoMediaItemManager $videoMediaItemManager,
        private MediaItemResponseMapper $mediaItemResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: VideoMediaItemRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MediaItemDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(name: 'videos_media_item_upload', methods: ['POST'])]
    #[ParamConverter(
        data: 'videoMediaItemRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body')
    ]
    public function update(
        #[OA\PathParameter] string $videoId,
        VideoMediaItemRequest $videoMediaItemRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $video = $this->videoProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $video);

        $videoMediaItem = $this->videoMediaItemManager->createForVideo(
            $video,
            $videoMediaItemRequest->extension
        );

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->mediaItemResponseMapper->map($videoMediaItem->getMediaItem())
        );
    }
}
