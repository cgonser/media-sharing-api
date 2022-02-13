<?php

namespace App\User\Controller\BillingAddress;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\User\Entity\User;
use App\User\Provider\BillingAddressProvider;
use App\User\Service\BillingAddressManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/{userId}/billing_addresses')]
class BillingAddressDeleteController extends AbstractController
{
    public function __construct(
        private BillingAddressManager $billingAddressManager,
        private BillingAddressProvider $billingAddressProvider
    ) {
    }

    /**
     * @OA\Tag(name="User / Billing Address")
     * @OA\Response(response=204, description="Success")
     * @OA\Response(response=404, description="Resource not found")
     */
    #[Route('/{billingAddressId}', name: 'user_billing_address_delete', methods: 'DELETE')]
    #[ParamConverter(data: 'user', converter: 'user.user_entity')]
    public function delete(User $user, string $billingAddressId): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $user);

        $billingAddress = $this->billingAddressProvider->getByUserAndId(
            $user->getId(),
            Uuid::fromString($billingAddressId)
        );

        $this->billingAddressManager->delete($billingAddress);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
