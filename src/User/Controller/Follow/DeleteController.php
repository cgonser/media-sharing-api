<?php

namespace App\User\Controller\Follow;

use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\Service\UserFollowManager;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Follow')]
#[Route(path: '/users/current/follows')]
class DeleteController extends AbstractController
{
    public function __construct(
        private UserFollowManager $userFollowManager,
        private UserProvider $userProvider,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Resource not found")]
    #[Route(path: '/{followingId}', name: 'user_follow_delete', methods: 'DELETE')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function delete(#[OA\PathParameter] string $followingId, User $user): Response
    {
        $following = $this->userProvider->get(Uuid::fromString($followingId));

        $this->userFollowManager->unfollow($user, $following);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
