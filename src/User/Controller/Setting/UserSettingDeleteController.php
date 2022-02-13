<?php

namespace App\User\Controller\Setting;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Exception\UserSettingNotFoundException;
use App\User\Service\UserSettingManager;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{userId}/settings')]
class UserSettingDeleteController extends AbstractController
{
    public function __construct(
        private UserSettingManager $userSettingManager,
    ) {
    }

    /**
     * @OA\Tag(name="User / Settings")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route('/{name}', name: 'user_settings_delete', methods: 'DELETE')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function delete(User $user, string $name): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $userSetting = $this->userSettingManager->get($user->getId(), $name);

        if (null === $userSetting) {
            throw new UserSettingNotFoundException();
        }

        $this->userSettingManager->delete($userSetting);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
