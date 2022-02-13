<?php

namespace App\User\Controller;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDemoController extends AbstractController
{
    /**
     * @OA\Tag(name="Demo")
     */
    #[Route(path: '/demo/users', name: 'user_demo_page', methods: ['GET'])]
    public function demo(): Response
    {
        return $this->render('user/demo.html.twig');
    }

    /**
     * @OA\Tag(name="Demo")
     */
    #[Route(path: '/demo/users/login/facebook', name: 'user_facebook_login_button', methods: ['GET'])]
    public function facebookLoginButton(): Response
    {
        return $this->render('user/facebook_login.html.twig');
    }

    /**
     * @OA\Tag(name="Demo")
     */
    #[Route(path: '/demo/users/email-verification/{token}', name: 'user_email_verify_form', methods: ['GET'])]
    public function emailVerificationForm(string $token): Response
    {
        return $this->render(
            'user/email_verification.html.twig',
            [
                'token' => $token,
            ]
        );
    }

    /**
     * @OA\Tag(name="Demo")
     */
    #[Route(path: '/demo/users/password-reset/{token}', name: 'user_password_reset_form', methods: ['GET'])]
    public function resetPasswordForm(string $token): Response
    {
        if ('form' === $token) {
            return $this->render(
                'user/password_reset_request.html.twig',
                [
                    'token' => $token,
                ]
            );
        }

        return $this->render(
            'user/password_reset.html.twig',
            [
                'token' => $token,
            ]
        );
    }
}
