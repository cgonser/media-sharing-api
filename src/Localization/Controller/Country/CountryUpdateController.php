<?php

namespace App\Localization\Controller\Country;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CountryDto;
use App\Localization\Provider\CountryProvider;
use App\Localization\Request\CountryRequest;
use App\Localization\ResponseMapper\CountryResponseMapper;
use App\Localization\Service\CountryRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CountryUpdateController extends AbstractController
{
    public function __construct(
        private CountryRequestManager $countryRequestManager,
        private CountryProvider $countryProvider,
        private CountryResponseMapper $countryResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Localization / Country")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CountryRequest::class)))
     * @OA\Response(response=200, description="Updates a country", @OA\JsonContent(ref=@Model(type=CountryDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/countries/{code}', name: 'countries_update', methods: ['PUT'])]
    #[ParamConverter(
        data: 'countryRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        string $code,
        CountryRequest $countryRequest,
        ConstraintViolationListInterface $validationErrors
    ): Response {
        // TODO: implement authorization (admin only)
        $country = $this->countryProvider->getByCode($code);
        $this->countryRequestManager->updateFromRequest($country, $countryRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->countryResponseMapper->map($country)
        );
    }
}
