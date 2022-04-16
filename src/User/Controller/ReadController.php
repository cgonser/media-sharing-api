<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
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
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: UserDto::class)))
    )]
    #[Route(path: '/users', name: 'users_get', methods: ['GET'])]
    public function findUsers(): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, User::class);

        $users = $this->userProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userResponseMapper->mapMultiple($users)
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserDto::class))
    )]
    #[Route(path: '/users/{userId}', name: 'users_get_one', methods: ['GET'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserById(User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $user->getId()->equals($this->getUser()->getId())
                ? $this->userResponseMapper->map($user)
                : $this->userResponseMapper->mapPublic($user)
        );
    }
}
