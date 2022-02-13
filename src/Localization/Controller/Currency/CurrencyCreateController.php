<?php

namespace App\Localization\Controller\Currency;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Core\Response\ApiJsonResponse;
use App\Localization\Dto\CurrencyDto;
use App\Localization\Request\CurrencyRequest;
use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Localization\Service\CurrencyRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CurrencyCreateController extends AbstractController
{
    public function __construct(
        private CurrencyRequestManager $currencyRequestManager,
        private CurrencyResponseMapper $currencyResponseMapper
    ) {
    }

    /**
     * @OA\Tag(name="Localization / Currency")
     * @OA\RequestBody(required=true, @OA\JsonContent(ref=@Model(type=CurrencyRequest::class)))
     * @OA\Response(response=201, description="Success", @OA\JsonContent(ref=@Model(type=CurrencyDto::class)))
     * @OA\Response(response=400, description="Invalid input")
     */
    #[Route(path: '/currencies', name: 'currencies_create', methods: ['POST'])]
    #[ParamConverter(
        data: 'currencyRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function create(CurrencyRequest $currencyRequest, ConstraintViolationListInterface $validationErrors): Response
    {
        // TODO: implement authorization (admin only)
        $currency = $this->currencyRequestManager->createFromRequest($currencyRequest);

        return new ApiJsonResponse(
            Response::HTTP_CREATED,
            $this->currencyResponseMapper->map($currency)
        );
    }
}
