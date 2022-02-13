<?php

namespace App\User\Controller;

use App\User\Entity\User;
use App\User\Provider\UserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserImpersonateController extends AbstractController
{
    public function __construct(
        private UserProvider $userProvider,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
    ) {
    }

    /**
     * @OA\Tag(name="User / Authentication")
     * @OA\Response(response=201, description="Impersonates another user")
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/users/{identifier}/impersonation', name: 'users_impersonate', methods: ['POST'])]
    public function impersonate(string $identifier): Response
    {
        if (!$this->getUser()->hasRole(User::ROLE_ADMIN)) {
            throw new AccessDeniedHttpException();
        }

        $user = $this->userProvider->findOneByEmail($identifier)
            ?: $this->userProvider->get(Uuid::fromString($identifier));

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
    }
}
