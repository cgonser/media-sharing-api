<?php

namespace App\Media\Controller\Moment\MediaItem;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MediaItemDto;
use App\Media\Enumeration\MediaItemType;
use App\Media\Provider\MomentProvider;
use App\Media\ResponseMapper\MediaItemResponseMapper;
use App\Media\Service\MomentMediaItemManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Moment / Media Item')]
#[Route(path: '/moments/{momentId}/media_items')]
class UploadController extends AbstractController
{
    public function __construct(
        private readonly MomentProvider $momentProvider,
        private readonly MomentMediaItemManager $momentMediaItemManager,
        private readonly MediaItemResponseMapper $mediaItemResponseMapper,
    ) {
    }

    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'application/octet-stream',
            schema: new OA\Schema(type: 'string', format: 'binary')
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MediaItemDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/video', name: 'moments_media_item_upload', methods: ['PUT'])]
    public function create(
        #[OA\PathParameter] string $momentId,
        Request $request,
    ): Response {
        $moment = $this->momentProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($momentId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $moment);

        $momentMediaItem = $this->momentMediaItemManager->uploadItem(
            $moment,
            MediaItemType::VIDEO_ORIGINAL,
            $request->headers->get('Content-Type'),
            $request->getContent()
        );

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->mediaItemResponseMapper->map($momentMediaItem->getMediaItem())
        );
    }
}
