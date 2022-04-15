<?php

namespace App\User\Controller\Follow;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserFollowDto;
use App\User\Entity\User;
use App\User\Provider\UserProvider;
use App\User\ResponseMapper\UserFollowResponseMapper;
use App\User\Service\UserFollowManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Follow')]
#[Route(path: '/users/current/follows')]
class CreateController extends AbstractController
{
    public function __construct(
        private UserFollowResponseMapper $userFollowResponseMapper,
        private UserFollowManager $userFollowManager,
        private UserProvider $userProvider,
    ) {
    }

    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: UserFollowDto::class))
    )]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(path: '/{followingId}', name: 'user_follow_create', methods: 'POST')]
    public function create(#[OA\PathParameter] string $followingId): Response
    {
        $following = $this->userProvider->get(Uuid::fromString($followingId));

        $userFollow = $this->userFollowManager->follow($this->getUser(), $following);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->userFollowResponseMapper->map($userFollow)
        );
    }
}
