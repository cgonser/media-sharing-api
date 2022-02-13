<?php

namespace App\User\Controller;

use App\User\Request\UserFacebookLoginRequest;
use App\User\Service\UserFacebookLoginManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserFacebookLoginController extends AbstractController
{
    public function __construct(
        private UserFacebookLoginManager $userFacebookLoginManager,
        private AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {
    }

    /**
     * @OA\Tag(name="User / Authentication")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserFacebookLoginRequest::class)))
     * @OA\Response(response=200, description="Returns the API access token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/users/login/facebook', name: 'user_facebook_login', methods: ['POST'])]
    #[ParamConverter(
        data: 'userLoginFacebookRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function facebookLogin(UserFacebookLoginRequest $userFacebookLoginRequest, Request $request): Response
    {
        $user = $this->userFacebookLoginManager->prepareUserFromFacebookToken(
            $userFacebookLoginRequest->accessToken,
            $this->getUser(),
            $request->getClientIp()
        );

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
    }
}
