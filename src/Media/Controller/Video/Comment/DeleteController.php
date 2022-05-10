<?php

namespace App\Media\Controller\Video\Comment;

use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Provider\VideoCommentProvider;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoCommentManager;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video / Comments')]
#[Route(path: '/videos/{videoId}/comments')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly VideoProvider $videoProvider,
        private readonly VideoCommentProvider $videoCommentProvider,
        private readonly VideoCommentManager $videoCommentManager,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{videoCommentId}', name: 'videos_comments_delete', methods: ['DELETE'])]
    public function update(
        #[OA\PathParameter] string $videoId,
        #[OA\PathParameter] string $videoCommentId,
    ): Response {
        $video = $this->videoProvider->get(Uuid::fromString($videoId));
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $videoComment = $this->videoCommentProvider->getByVideoAndId(
            $video->getId(),
            Uuid::fromString($videoCommentId)
        );
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $videoComment);

        $this->videoCommentManager->delete($videoComment);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
