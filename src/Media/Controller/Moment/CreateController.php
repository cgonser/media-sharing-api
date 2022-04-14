<?php

namespace App\Media\Controller\Moment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use App\Media\Request\MomentRequest;
use App\Media\ResponseMapper\MomentResponseMapper;
use App\Media\Service\MomentRequestManager;
use App\User\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[OA\Tag(name: 'Moment')]
#[Route(path: '/moments')]
class CreateController extends AbstractController
{
    public function __construct(
        private MomentRequestManager $momentManager,
        private MomentResponseMapper $momentResponseMapper,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MomentRequest::class)))]
    #[OA\Response(
        response: 201,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MomentDto::class)))
    ]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(name: 'moments_create', methods: ['POST'])]
    #[ParamConverter(
        data: 'momentRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body')
    ]
    public function create(
        MomentRequest $momentRequest,
        ConstraintViolationListInterface $validationErrors,
    ): Response {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::CREATE, Moment::class);

        if (!$momentRequest->has('userId') || !$this->getUser()->hasRole(User::ROLE_ADMIN)) {
            $momentRequest->userId = $this->getUser()->getId()->toString();
        }

        $moment = $this->momentManager->createFromRequest($momentRequest);

        return new ApiJsonResponse(Response::HTTP_CREATED, $this->momentResponseMapper->map($moment));
    }
}
