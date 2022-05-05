<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\PublicUserDto;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use App\User\Provider\UserProvider;
use App\User\Request\UserSearchRequest;
use App\User\ResponseMapper\UserResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User')]
class ReadController extends AbstractController
{
    public function __construct(
        private UserProvider $userProvider,
        private UserResponseMapper $userResponseMapper,
        private UserFollowProvider $userFollowProvider,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: PublicUserDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(path: '/users', name: 'users_get', methods: ['GET'])]
    public function findUsers(UserSearchRequest $searchRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, User::class);

        $users = $this->userProvider->search($searchRequest);
        $followingIds = array_map(
            fn ($userFollowing) => $userFollowing->getFollowingId()->toString(),
            $this->userFollowProvider->findByFollowerId($this->getUser()->getId()),
        );

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->mapMultiple($users, $followingIds)
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserDto::class))
    )]
    #[Route(path: '/users/current', name: 'users_get_current', methods: ['GET'])]
    public function getCurrentUser(): Response
    {
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->map($this->getUser())
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: PublicUserDto::class))
    )]
    #[Route(
        path: '/users/{userId}',
        name: 'users_get_one',
        requirements: [ 'userId' => '%routing.uuid_mask%' ],
        methods: ['GET'],
    )]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserById(User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->mapPublic($user)
        );
    }
}
