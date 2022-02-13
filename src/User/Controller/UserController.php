<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\ResponseMapper\UserResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserProvider $userProvider,
        private UserResponseMapper $userResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="User")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=UserDto::class))))
     * )
     * @Security(name="Bearer")
     */
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

    /**
     * @Security(name="Bearer")
     * @OA\Tag(name="User")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=UserDto::class)))
     */
    #[Route(path: '/users/{userId}', name: 'users_get_one', methods: ['GET'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserById(User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        return new ApiJsonResponse(Response::HTTP_OK, $this->userResponseMapper->map($user));
    }
}
