<?php

namespace App\Media\Controller\Moment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Provider\MomentProvider;
use App\Media\Service\MomentManager;
use App\User\Entity\User;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Moment')]
#[Route(path: '/moments')]
class DeleteController extends AbstractController
{
    public function __construct(
        private MomentProvider $momentProvider,
        private MomentManager $momentManager,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Media not found")]
    #[Route(path: '/{momentId}', name: 'moments_delete', methods: ['DELETE'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function delete(User $user, #[OA\PathParameter] string $momentId): Response
    {
        $moment = $this->momentProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($momentId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::DELETE, $moment);

        $this->momentManager->delete($moment);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
