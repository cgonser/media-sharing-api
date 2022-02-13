<?php

namespace App\Localization\Controller\Country;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CountryDto;
use App\Localization\Provider\CountryProvider;
use App\Localization\Request\CountrySearchRequest;
use App\Localization\ResponseMapper\CountryResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    public function __construct(
        private CountryProvider $countryProvider,
        private CountryResponseMapper $countryResponseMapper
    ) {
    }

    /**
     * @ParamConverter("searchRequest", converter="querystring")
     *
     * @OA\Tag(name="Localization / Country")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CountryDto::class))))
     * )
     */
    #[Route('/countries', methods: ['GET'])]
    public function getCountries(CountrySearchRequest $searchRequest): Response
    {
        $countries = $this->countryProvider->search($searchRequest);
        $count = $this->countryProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->mapMultiple($countries),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Localization / Country")
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=CountryDto::class))))
     * )
     */
    #[Route('/countries/codes', methods: ['GET'], priority: 10)]
    public function getCountryCodes(CountrySearchRequest $searchRequest): Response
    {
        $searchRequest->resultsPerPage = 9999;

        $countries = $this->countryProvider->search($searchRequest);
        $count = $this->countryProvider->count($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->mapMultipleCodes($countries),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    /**
     * @OA\Tag(name="Localization / Country")
     * @OA\Response(response=200, description="Success", @OA\JsonContent(ref=@Model(type=CountryDto::class)))
     */
    #[Route(path: '/countries/{code}', name: 'countries_get_by_code', methods: ['GET'], priority: 0)]
    public function getCountryByCode(string $code): Response
    {
        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->map($this->countryProvider->getByCode($code))
        );
    }
}
