<?php

namespace App\Media\Controller\Video;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Provider\VideoProvider;
use App\Media\Service\VideoManager;
use App\User\Entity\User;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Video')]
#[Route(path: '/videos')]
class DeleteController extends AbstractController
{
    public function __construct(
        private VideoProvider $videoProvider,
        private VideoManager $videoManager,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Media not found")]
    #[Route(path: '/{videoId}', name: 'videos_delete', methods: ['DELETE'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function delete(User $user, #[OA\PathParameter] string $videoId): Response
    {
        $video = $this->videoProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($videoId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::DELETE, $video);

        $this->videoManager->delete($video);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}