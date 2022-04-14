<?php

namespace App\User\Controller\BillingAddress;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\BillingAddressDto;
use App\User\Entity\User;
use App\User\Request\BillingAddressRequest;
use App\User\ResponseMapper\BillingAddressResponseMapper;
use App\User\Service\BillingAddressRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User / Billing Address')]
#[Route(path: '/users/{userId}/billing_addresses')]
class CreateController extends AbstractController
{
    public function __construct(
        private BillingAddressResponseMapper $billingAddressResponseMapper,
        private BillingAddressRequestManager $billingAddressManager,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: BillingAddressRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: BillingAddressDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'user_billing_address_create', methods: 'POST')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter(
        data: 'billingAddressRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(User $user, BillingAddressRequest $billingAddressRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $billingAddressRequest->userId = $user->getId();
        $billingAddress = $this->billingAddressManager->createFromRequest($billingAddressRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->billingAddressResponseMapper->map($billingAddress)
        );
    }
}
