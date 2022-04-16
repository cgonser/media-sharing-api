<?php

namespace App\Media\Controller\Moment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MomentDto;
use App\Media\Provider\MomentProvider;
use App\Media\Request\MomentRequest;
use App\Media\ResponseMapper\MomentResponseMapper;
use App\Media\Service\MomentRequestManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Moment')]
#[Route(path: '/moments')]
class UpdateController extends AbstractController
{
    public function __construct(
        private MomentProvider $momentProvider,
        private MomentRequestManager $momentManager,
        private MomentResponseMapper $momentResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MomentRequest::class)))]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MomentDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[OA\Response(response: 404, description: "Not found")]
    #[Route(path: '/{momentId}', name: 'moments_update', methods: ['PATCH', 'PUT'])]
    #[ParamConverter(
        data: 'momentRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    public function update(
        #[OA\PathParameter] string $momentId,
        MomentRequest $momentRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $moment = $this->momentProvider->getByUserAndId(
            $this->getUser()->getId(),
            Uuid::fromString($momentId)
        );

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::UPDATE, $moment);

        $this->momentManager->updateFromRequest($moment, $momentRequest);

        return new ApiJsonResponse(Response::HTTP_OK, $this->momentResponseMapper->map($moment));
    }
}
