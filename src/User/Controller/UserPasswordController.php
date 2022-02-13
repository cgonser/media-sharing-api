<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Request\UserPasswordChangeRequest;
use App\User\Request\UserPasswordResetRequest;
use App\User\Request\UserPasswordResetTokenRequest;
use App\User\Service\UserRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPasswordController extends AbstractController
{
    public function __construct(
        private UserRequestManager $userManager,
    ) {
    }

    /**
     * @OA\Tag(name="User / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserPasswordChangeRequest::class)))
     * @OA\Response(response=204, description="Updates the current user's password")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[ParamConverter(
        data: 'userPasswordChangeRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    #[Route(path: '/users/{userId}/password', name: 'user_password_change', methods: ['PUT'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function changePassword(User $user, UserPasswordChangeRequest $userPasswordChangeRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $this->userManager->changePassword($user, $userPasswordChangeRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Tag(name="User / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserPasswordResetRequest::class)))
     * @OA\Response(response=200, description="Password change requested")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/users/password-reset', name: 'user_password_reset', methods: ['POST'])]
    #[ParamConverter(
        data: 'userPasswordResetRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function resetPassword(UserPasswordResetRequest $userPasswordResetRequest): Response
    {
        $this->userManager->startPasswordReset($userPasswordResetRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }

    /**
     * @OA\Tag(name="User / Password")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserPasswordResetTokenRequest::class)))
     * @OA\Response(response=200, description="Password changed")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/users/password-reset/token', name: 'user_password_reset_token', methods: ['POST'])]
    #[ParamConverter(
        data: 'userPasswordResetTokenRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function resetPasswordToken(UserPasswordResetTokenRequest $userPasswordResetTokenRequest): Response
    {
        $this->userManager->concludePasswordReset($userPasswordResetTokenRequest);

        return new ApiJsonResponse(Response::HTTP_OK);
    }
}
