<?php

namespace App\User\Controller\Integration;

use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Request\UserIntegrationRequest;
use App\User\Service\UserIntegrationRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/users/{userId}/integrations')]
class UserIntegrationCreateController extends AbstractController
{
    public function __construct(
        private UserIntegrationRequestManager $userIntegrationRequestManager,
    ) {
    }

    /**
     * @OA\Tag(name="User / Integrations")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=UserIntegrationRequest::class)))
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Invalid credentials")
     */
    #[Route(name: 'user_integration_post', methods: ['POST'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter(
        data: 'userIntegrationRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        User $user,
        UserIntegrationRequest $userIntegrationRequest
    ): Response {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userIntegrationRequest->userId = $user->getId()->toString();

        $this->userIntegrationRequestManager->createOrUpdateFromRequest($userIntegrationRequest);

        return new Response(null, 204);
    }
}
