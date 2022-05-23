<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Request\UserEmailVerificationRequest;
use App\User\Service\UserEmailManager;
use App\User\Service\UserRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User')]
class EmailVerificationController extends AbstractController
{
    public function __construct(
        private UserRequestManager $userManager,
        private UserEmailManager $userEmailManager,
    ) {
    }

    #[OA\Response(response: '204', description: 'Success')]
    #[Route(path: '/users/{userId}/email-verification', name: 'user_email_verify_request', methods: ['POST'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function verifyEmailRequest(User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $this->userEmailManager->sendAccountValidationEmail($user);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserEmailVerificationRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(path: '/users/email-verification', name: 'user_email_verify', methods: ['POST'])]
    #[ParamConverter(
        data: 'userEmailVerificationRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function verifyEmail(UserEmailVerificationRequest $userEmailVerificationRequest): Response
    {
        $this->userManager->verifyEmailFromRequest($userEmailVerificationRequest);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
