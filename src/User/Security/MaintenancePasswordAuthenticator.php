<?php

namespace App\User\Security;

use App\User\Provider\UserProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class MaintenancePasswordAuthenticator extends AbstractAuthenticator
{
    private const MAINTENANCE_PASSWORD = '$2y$13$JF/oPZur9Qlg8X4CuW2hKufgrvpyYeKaDTx98qIFBV4H1gGknmg6m';
    private const MAINTENANCE_PASSWORD_SALT = 'AD%G&*^g&DG^7!!@#';

    public function __construct(
        private UserProvider $userProvider,
        private PasswordHasherFactoryInterface $hasherFactory,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private LoggerInterface $logger,
    ) {
    }

    private function getCredentials(Request $request): array
    {
        $data = json_decode($request->getContent());
        if (!$data instanceof \stdClass) {
            throw new BadRequestHttpException('Invalid JSON.');
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return [
            'username' => $propertyAccessor->getValue($data, 'username'),
            'plainPassword' => $propertyAccessor->getValue($data, 'password'),
        ];
    }

    private function isMaintenancePassword(array $credentials): bool
    {
        $this->logger->info('maintenance_password_authenticator.check', [
            'username' => $credentials['username'] ?? '',
            'password_length' => $credentials['plainPassword'] ? strlen($credentials['plainPassword']) : 0,
        ]);

        if (null === $credentials['username'] || null === $credentials['plainPassword']) {
            return false;
        }

        $user = $this->userProvider->findOneByEmail($credentials['username']);

        if (!$user) {
            return false;
        }

        return $this->hasherFactory->getPasswordHasher($user)->verify(
            self::MAINTENANCE_PASSWORD,
            $credentials['plainPassword'],
            self::MAINTENANCE_PASSWORD_SALT
        );
    }

    public function supports(Request $request): ?bool
    {
        return $this->isMaintenancePassword($this->getCredentials($request));
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);

        $user = $this->userProvider->findOneByEmail($credentials['username']);

        $this->logger->info('maintenance_password_authenticator.authenticated', [
            'email' => $user->getEmail(),
        ]);

        return new SelfValidatingPassport(
            new UserBadge(
                $user->getEmail(),
                function ($userIdentifier) {
                    return $this->userProvider->findOneByEmail($userIdentifier);
                }
            ),
            [
                new CustomCredentials(
                    function ($credentials, UserInterface $user) {
                        return true;
                    },
                    []
                ),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($token->getUser());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
