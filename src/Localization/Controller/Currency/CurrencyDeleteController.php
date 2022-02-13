<?php

namespace App\Localization\Controller\Currency;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Service\CurrencyManager;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyDeleteController extends AbstractController
{
    public function __construct(
        private CurrencyManager $currencyService,
        private CurrencyProvider $currencyProvider
    ) {
    }

    /**
     * @OA\Tag(name="Localization / Currency")
     * @OA\Response(response=204, description="Deletes a currency")
     * @OA\Response(response=404, description="Currency not found")
     */
    #[Route(path: '/currencies/{currencyId}', name: 'currencies_delete', methods: ['DELETE'])]
    public function delete(string $currencyId): Response
    {
        // TODO: implement authorization (admin only)
        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));
        $this->currencyService->delete($currency);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
