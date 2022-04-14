<?php

namespace App\User\Controller\Integration;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserIntegrationDto;
use App\User\Dto\UserIntegrationStatusDto;
use App\User\Entity\User;
use App\User\Provider\UserIntegrationProvider;
use App\User\ResponseMapper\UserIntegrationResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Integrations')]
#[Route(path: '/users/{userId}/integrations')]
class ReadController extends AbstractController
{
    public function __construct(
        private UserIntegrationProvider $userIntegrationProvider,
        private UserIntegrationResponseMapper $userIntegrationResponseMapper,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserIntegrationStatusDto::class))
    )]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(name: 'user_integration_get_status', methods: ['GET'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getByUserIntegrationStatus(User $user): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        return new ApiJsonResponse(
            200,
            $this->userIntegrationResponseMapper->mapStatus(
                $user,
                $this->userIntegrationProvider->findByUser($user->getId())
            )
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserIntegrationDto::class))
    )]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{platform}', name: 'user_integration_get', methods: ['GET'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getByUserAndPlatform(User $user, string $platform): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        $userIntegration = $this->userIntegrationProvider->getByUserAndPlatform($user->getId(), $platform);

        return new ApiJsonResponse(
            200,
            $this->userIntegrationResponseMapper->map($userIntegration)
        );
    }
}
