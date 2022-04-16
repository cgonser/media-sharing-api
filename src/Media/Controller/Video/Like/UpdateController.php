<?php

namespace App\Media\Controller\Video\Like;

use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoLikeManager;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video / Likes')]
#[Route(path: '/videos/{videoId}/likes')]
class UpdateController extends AbstractController
{
    public function __construct(
        private VideoProvider $videoProvider,
        private VideoLikeManager $videoLikeManager,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(name: 'videos_like', methods: ['PUT'])]
    public function like(#[OA\PathParameter] string $videoId): Response
    {
        $video = $this->videoProvider->get(Uuid::fromString($videoId));

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $video);

        $this->videoLikeManager->like($video, $this->getUser());

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
