<?php

namespace App\User\Controller\BillingAddress;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\BillingAddressDto;
use App\User\Entity\User;
use App\User\Provider\BillingAddressProvider;
use App\User\Request\BillingAddressSearchRequest;
use App\User\ResponseMapper\BillingAddressResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Billing Address')]
#[Route(path: '/users/{userId}/billing_addresses')]
class ReadController extends AbstractController
{
    public function __construct(
        private BillingAddressProvider $billingAddressProvider,
        private BillingAddressResponseMapper $billingAddressResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: BillingAddressSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        headers: [
            new OA\Header(header: "X-Total-Count", schema: new OA\Schema(type: "int")),
        ],
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: BillingAddressDto::class)))
    )]
    #[Route(name: 'user_billing_address_find', methods: 'GET')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter('searchRequest', converter: 'querystring')]
    public function getBillingAddressList(User $user, BillingAddressSearchRequest $searchRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $user);

        $searchRequest->userId = $user->getId();

        $results = $this->billingAddressProvider->search($searchRequest);
        $count = $this->billingAddressProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingAddressResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }
}
