<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserDto;
use App\User\Entity\User;
use App\User\Request\UserRequest;
use App\User\ResponseMapper\UserResponseMapper;
use App\User\Service\UserRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserUpdateController extends AbstractController
{
    public function __construct(
        private UserRequestManager $userManager,
        private UserResponseMapper $userResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="User")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserRequest::class)))
     * @OA\Response(response=200, description="Updates a user", @OA\JsonContent(ref=@Model(type=UserDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/users/{userId}', name: 'users_update', methods: ['PUT', 'PATCH'])]
    #[ParamConverter(
        data: 'userRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function update(User $user, UserRequest $userRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $this->userManager->updateFromRequest($user, $userRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->userResponseMapper->map($user));
    }
}
