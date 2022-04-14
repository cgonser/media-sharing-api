<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\User\Dto\UserDto;
use App\User\Request\UserRequest;
use App\User\ResponseMapper\UserResponseMapper;
use App\User\Service\UserRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'User')]
class CreateController extends AbstractController
{
    public function __construct(
        private UserRequestManager $userManager,
        private UserResponseMapper $userResponseMapper
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(path: '/users', name: 'users_create', methods: ['POST'])]
    #[ParamConverter(
        data: 'userRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        UserRequest $userRequest,
        Request $request,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $user = $this->userManager->createFromRequest(
            $userRequest,
            $request->getClientIp(),
        );

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->userResponseMapper->map($user));
    }
}
