<?php

namespace App\User\Controller\BillingAddress;

use App\Core\Request\SearchRequest;
use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\BillingAddressDto;
use App\User\Entity\User;
use App\User\Provider\BillingAddressProvider;
use App\User\Request\BillingAddressSearchRequest;
use App\User\ResponseMapper\BillingAddressResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{userId}/billing_addresses')]
class BillingAddressController extends AbstractController
{
    public function __construct(
        private BillingAddressProvider $billingAddressProvider,
        private BillingAddressResponseMapper $billingAddressResponseMapper,
    ) {
    }

    /**
     * @OA\Tag(name="User / Billing Address")
     * @OA\Parameter(in="query", name="filters", @OA\Schema(ref=@Model(type=SearchRequest::class)))
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\Header(header="X-Total-Count", @OA\Schema(type="int")),
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=BillingAddressDto::class))))
     * )
     * @Security(name="Bearer")
     */
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
