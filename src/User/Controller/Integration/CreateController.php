<?php

namespace App\User\Controller\Integration;

use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Request\UserIntegrationRequest;
use App\User\Service\UserIntegrationRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'User / Integrations')]
#[Route(path: '/users/{userId}/integrations')]
class CreateController extends AbstractController
{
    public function __construct(
        private UserIntegrationRequestManager $userIntegrationRequestManager,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserIntegrationRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'user_integration_post', methods: ['POST'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter(
        data: 'userIntegrationRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(
        User $user,
        UserIntegrationRequest $userIntegrationRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userIntegrationRequest->userId = $user->getId()->toString();

        $this->userIntegrationRequestManager->createOrUpdateFromRequest($userIntegrationRequest);

        return new Response(null, 204);
    }
}
