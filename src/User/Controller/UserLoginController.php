<?php

namespace App\User\Controller;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserLoginController extends AbstractController
{
    public function __construct(private RefreshToken $refreshTokenService)
    {
    }

    /**
     * @OA\Tag(name="User / Authentication")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         required={"username", "password"},
     *         @OA\Property(property="username", type="string"),
     *         @OA\Property(property="password", type="string")
     *     )
     * )
     * @OA\Response(response=200, description="Provides the authentication token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     * @OA\Response(response=412, description="Password reset required")
     */
    #[Route(path: '/users/login', name: 'user_login_check', methods: ['POST'])]
    public function login()
    {
    }

    /**
     * @OA\Tag(name="User / Authentication")
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(type="object", @OA\Property(property="refresh_token", type="string"))
     *     )
     * )
     * @OA\Response(response=200, description="Provides the refreshed token")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(path: '/users/token/refresh', name: 'user_token_refresh', methods: ['POST'])]
    public function tokenRefresh(Request $request)
    {
        return $this->refreshTokenService->refresh($request);
    }
}
