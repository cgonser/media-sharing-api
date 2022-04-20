<?php

namespace App\Media\Controller\Moment\MediaItem;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MediaItemDto;
use App\Media\Provider\MomentProvider;
use App\Media\Request\MomentMediaItemRequest;
use App\Media\ResponseMapper\MediaItemResponseMapper;
use App\Media\Service\MomentMediaItemManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Moment / Media Item')]
#[Route(path: '/moments/{momentId}/media_items')]
class CreateController extends AbstractController
{
    public function __construct(
        private MomentProvider $momentProvider,
        private MomentMediaItemManager $momentMediaItemManager,
        private MediaItemResponseMapper $mediaItemResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MomentMediaItemRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MediaItemDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(name: 'moments_media_item_upload', methods: ['POST'])]
    #[ParamConverter(
        data: 'momentMediaItemRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body')
    ]
    public function update(
        #[OA\PathParameter] string $momentId,
        MomentMediaItemRequest $momentMediaItemRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $moment = $this->momentProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($momentId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $moment);

        $momentMediaItem = $this->momentMediaItemManager->createForMoment(
            $moment,
            $momentMediaItemRequest->extension
        );

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->mediaItemResponseMapper->map($momentMediaItem->getMediaItem())
        );
    }
}
