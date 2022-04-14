<?php

namespace App\Localization\Controller\Currency;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\ResponseMapper\CurrencyResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "Localization / Currency")]
class ReadController extends AbstractController
{
    public function __construct(
        private CurrencyProvider $currencyProvider,
        private CurrencyResponseMapper $currencyResponseMapper,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: CurrencyDto::class)))
    )]
    #[Route(path: '/currencies', name: 'currencies_get', methods: ['GET'])]
    public function getCurrencies(): Response
    {
        $currencies = $this->currencyProvider->findAll();

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->mapMultiple($currencies)
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: CurrencyDto::class))
    )]    #[Route(
        path: '/currencies/{currencyId}',
        name: 'currencies_get_by_id',
        requirements: ['"currencyId"' => '%routing.uuid%'],
        methods: ['GET']
    )]
    public function getCurrencyById(#[OA\PathParameter] string $currencyId): Response
    {
        $currency = $this->currencyProvider->get(Uuid::fromString($currencyId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: CurrencyDto::class))
    )]
    #[Route(
        path: '/currencies/{currencyCode}',
        name: 'currencies_get_by_code',
        requirements: ['"currencyCode"' => '\w+'],
        methods: ['GET']
    )]
    public function getCurrencyByCode(#[OA\PathParameter] string $currencyCode): Response
    {
        $currency = $this->currencyProvider->getByCode($currencyCode);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
