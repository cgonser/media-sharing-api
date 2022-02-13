<?php

namespace App\Localization\Controller\Currency;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\ResponseMapper\CurrencyResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends AbstractController
{
    public function __construct(
        private CurrencyProvider $currencyProvider,
        private CurrencyResponseMapper $currencyResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Localization / Currency")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CurrencyDto::class))))
     * )
     */
    #[Route(path: '/currencies', name: 'currencies_get', methods: ['GET'])]
    public function getCurrencies(): Response
    {
        $currencies = $this->currencyProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->mapMultiple($currencies)
        );
    }

    /**
     * @OA\Tag(name="Localization / Currency")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CurrencyDto::class)))
     */
    #[Route(
        path: '/currencies/{currencyId}',
        name: 'currencies_get_by_id',
        requirements: ['"currencyId"' => '%routing.uuid%'],
        methods: ['GET']
    )]
    public function getCurrencyById(string $currencyId): Response
    {
        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }

    /**
     * @OA\Tag(name="Localization / Currency")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CurrencyDto::class)))
     */
    #[Route(
        path: '/currencies/{currencyCode}',
        name: 'currencies_get_by_code',
        requirements: ['"currencyCode"' => '\w+'],
        methods: ['GET']
    )]
    public function getCurrencyByCode(string $currencyCode): Response
    {
        $currency = $this->currencyProvider->getByCode($currencyCode);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
