<?php

namespace App\User\Controller\BillingAddress;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Dto\BillingAddressDto;
use App\User\Entity\User;
use App\User\Provider\BillingAddressProvider;
use App\User\Request\BillingAddressRequest;
use App\User\ResponseMapper\BillingAddressResponseMapper;
use App\User\Service\BillingAddressRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'User / Billing Address')]
#[Route(path: '/users/{userId}/billing_addresses')]
class UpdateController extends AbstractController
{
    public function __construct(
        private BillingAddressRequestManager $billingAddressManager,
        private BillingAddressProvider $billingAddressProvider,
        private BillingAddressResponseMapper $billingAddressResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: BillingAddressRequest::class)))]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: BillingAddressDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{billingAddressId}', name: 'user_billing_address_update', methods: ['PATCH', 'PUT'])]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    #[ParamConverter(
        data: 'billingAddressRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        #[OA\PathParameter] string $billingAddressId,
        BillingAddressRequest $billingAddressRequest,
        User $user,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $billingAddress = $this->billingAddressProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($billingAddressId)
        );

        $billingAddressRequest->userId = $user->getId();
        $this->billingAddressManager->updateFromRequest($billingAddress, $billingAddressRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->billingAddressResponseMapper->map($billingAddress)
        );
    }
}
