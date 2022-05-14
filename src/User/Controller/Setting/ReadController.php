<?php

namespace App\User\Controller\Setting;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserSettingDto;
use App\User\Entity\User;
use App\User\Exception\UserSettingNotFoundException;
use App\User\ResponseMapper\UserSettingResponseMapper;
use App\User\Service\UserSettingManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Settings')]
#[Route(path: '/users/{userId}/settings')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly UserSettingResponseMapper $userSettingResponseMapper,
        private readonly UserSettingManager $userSettingManager,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserSettingDto::class))
    )]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route('/{name}', name: 'user_settings_get_one', methods: 'GET')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function getUserSetting(User $user, string $name): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        $userSetting = $this->userSettingManager->get($user->getId(), $name);

        if (null === $userSetting) {
            throw new UserSettingNotFoundException();
        }

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userSettingResponseMapper->map($userSetting),
        );
    }
}
