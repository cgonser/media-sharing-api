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
#[Route(path: '/users/current')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly UserFollowManager $userFollowManager,
        private readonly UserProvider $userProvider,
    ) {
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Resource not found")]
    #[Route(path: '/follows/{followingId}', name: 'user_follow_delete', methods: 'DELETE')]
    #[Route(path: '/following/{followingId}', name: 'user_following_delete', methods: 'DELETE')]
    public function unfollow(#[OA\PathParameter] string $followingId): Response
    {
        /** @var User $following */
        $following = $this->userProvider->get(Uuid::fromString($followingId));

        $this->userFollowManager->unfollow($this->getUser(), $following);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Resource not found")]
    #[Route(path: '/followers/{followerId}', name: 'user_follower_delete', methods: 'DELETE')]
    public function deleteFollower(#[OA\PathParameter] string $followerId): Response
    {
        /** @var User $follower */
        $follower = $this->userProvider->get(Uuid::fromString($followerId));

        $this->userFollowManager->unfollow($follower, $this->getUser());

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
