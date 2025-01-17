<?php

namespace App\User\Controller\Follow;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\UserFollowDto;
use App\User\Entity\User;
use App\User\Provider\UserFollowProvider;
use App\User\Request\UserFollowSearchRequest;
use App\User\ResponseMapper\UserFollowResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Follow')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly UserFollowProvider $userFollowProvider,
        private readonly UserFollowResponseMapper $userFollowResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: UserFollowSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        headers: [
            new OA\Header(header: "X-Total-Count", schema: new OA\Schema(type: "int")),
        ],
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: UserFollowDto::class)))
    )]
    #[Route(path: '/users/{userId}/follows', name: 'user_follow_find', methods: 'GET')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function find(User $user, UserFollowSearchRequest $searchRequest): Response
    {
        if ($user !== $this->getUser()) {
            $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

            $searchRequest->isApproved = true;
            $searchRequest->isPending = false;
        }

        $searchRequest->followerId = $user->getId()->toString();

        $results = $this->userFollowProvider->search($searchRequest);
        $count = $this->userFollowProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userFollowResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: UserFollowSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        headers: [
            new OA\Header(header: "X-Total-Count", schema: new OA\Schema(type: "int")),
        ],
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: UserFollowDto::class)))
    )]
    #[Route(path: '/users/{userId}/followers', name: 'user_followers_find', methods: 'GET')]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function findFollowers(User $user, UserFollowSearchRequest $searchRequest): Response
    {
        if ($user !== $this->getUser()) {
            $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

            $searchRequest->isApproved = true;
            $searchRequest->isPending = false;
        }

        $searchRequest->followingId = $user->getId()->toString();

        $results = $this->userFollowProvider->search($searchRequest);
        $count = $this->userFollowProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->userFollowResponseMapper->mapMultiple($results, false, true),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
