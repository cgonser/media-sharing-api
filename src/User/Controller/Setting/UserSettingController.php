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
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{userId}/settings')]
class UserSettingController extends AbstractController
{
    public function __construct(
        private UserSettingResponseMapper $userSettingResponseMapper,
        private UserSettingManager $userSettingManager
    ) {
    }

    /**
     * @OA\Tag(name="User / Settings")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=UserSettingDto::class)))
     * @Security(name="Bearer")
     */
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
