<?php

namespace App\Localization\Controller\Timezone;

use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\TimezoneDto;
use App\Localization\Provider\TimezoneProvider;
use App\Localization\Request\TimezoneSearchRequest;
use App\Localization\ResponseMapper\TimezoneResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "Localization / Timezone")]
class ReadController
{
    public function __construct(
        private TimezoneProvider $timezoneProvider,
        private TimezoneResponseMapper $timezoneResponseMapper,
    ) {
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: TimezoneDto::class)))
    )]
    #[Route(path: '/timezones', name: 'timezones_get', methods: ['GET'])]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function getTimezones(TimezoneSearchRequest $searchRequest): Response
    {
        if (null !== $searchRequest->countryCode) {
            $timezones = $this->timezoneProvider->findByCountryCode($searchRequest->countryCode);
        } else {
            $timezones = $this->timezoneProvider->findAll();
        }

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->timezoneResponseMapper->mapMultiple($timezones)
        );
    }
}
