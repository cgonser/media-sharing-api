<?php

namespace App\User\Controller\Follow;

use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use App\User\Service\UserFollowManager;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Follow')]
#[Route(path: '/users/{userId}/follows')]
class UpdateController extends AbstractController
{
    public function __construct(
        private UserFollowManager $userFollowManager,
        private UserFollowProvider $userFollowProvider,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{userFollowId}/approval', name: 'user_follow_approval', methods: ['PATCH'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function approve(#[OA\PathParameter] string $userFollowId, User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userFollow = $this->userFollowProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($userFollowId)
        );

        $this->userFollowManager->approve($userFollow);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{userFollowId}/refusal', name: 'user_follow_refusal', methods: ['PATCH'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function refuse(#[OA\PathParameter] string $userFollowId, User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userFollow = $this->userFollowProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($userFollowId)
        );

        $this->userFollowManager->refuse($userFollow);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
