<?php

namespace App\User\Controller\Setting;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserSettingDto;
use App\User\Entity\User;
use App\User\Request\UserSettingRequest;
use App\User\ResponseMapper\UserSettingResponseMapper;
use App\User\Service\UserSettingManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Settings')]
#[Route('/users/{userId}/settings')]
class UpdateController extends AbstractController
{
    public function __construct(
        private UserSettingManager $userSettingManager,
        private UserSettingResponseMapper $userSettingResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserSettingRequest::class)))]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserSettingDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route('/{name}', name: 'user_settings_update', methods: 'PUT')]
    #[ParamConverter(
        data: 'userSettingRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function update(User $user, string $name, UserSettingRequest $userSettingRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userSetting = $this->userSettingManager->set($user->getId(), $name, $userSettingRequest->value);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userSettingResponseMapper->map($userSetting)
        );
    }
}
