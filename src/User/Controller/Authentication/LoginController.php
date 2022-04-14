<?php

namespace App\User\Controller\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Authentication')]
class LoginController extends AbstractController
{
    public function __construct(private RefreshToken $refreshTokenService)
    {
    }

    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["username", "password"],
            properties: [
                new OA\Property(property: "username", type: "string"),
                new OA\Property(property: "password", type: "string"),
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 401, description: "Invalid Credentials")]
    #[OA\Response(response: 412, description: "Password reset required")]
    #[Route(path: '/users/login', name: 'user_login_check', methods: ['POST'])]
    public function login()
    {
    }

    #[OA\RequestBody(
        required: true,
        content: [
            new OA\MediaType(
                mediaType: "application/x-www-form-urlencoded",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "refresh_token", type: "string"),
                    ],
                    type: "object"
                )
            )
        ]
    )]
    #[OA\Response(response: 200, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 401, description: "Invalid Credentials")]
    #[Route(path: '/users/token/refresh', name: 'user_token_refresh', methods: ['POST'])]
    public function tokenRefresh(Request $request)
    {
        return $this->refreshTokenService->refresh($request);
    }
}
