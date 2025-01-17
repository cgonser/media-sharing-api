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
#[Route(path: '/users')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly UserProvider $userProvider,
        private readonly UserResponseMapper $userResponseMapper,
        private readonly UserFollowProvider $userFollowProvider,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: PublicUserDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'users_get', methods: ['GET'])]
    public function findUsers(UserSearchRequest $searchRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, User::class);

        if ($searchRequest->excludeCurrent) {
            $searchRequest->exclusions[] = $this->getUser()->getId()->toString();
        }

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
    #[Route(
        path: '/current',
        name: 'users_get_current',
        methods: ['GET'],
        priority: 100,
    )]
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
        path: '/{userId}',
        name: 'users_get_one_by_id',
        requirements: [ 'userId' => '%routing.uuid_mask%' ],
        methods: ['GET'],
        priority: 50,
    )]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserById(User $user): Response
    {
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->mapPublic(
                $user,
                $this->userFollowProvider->isFollowing($this->getUser()->getId(), $user->getId(), null),
            )
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: PublicUserDto::class))
    )]
    #[Route(
        path: '/{username}',
        name: 'users_get_one_by_username',
        methods: ['GET'],
        priority: 50,
    )]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserByUsername(User $user): Response
    {
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->mapPublic(
                $user,
                $this->userFollowProvider->isFollowing($this->getUser()->getId(), $user->getId(), null),
            )
        );
    }
}
