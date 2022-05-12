<?php

namespace App\Media\Controller\Moment;

use App\Core\Response\ApiJsonResponse;
use App\Core\Security\AuthorizationVoterInterface;
use App\Media\Dto\MomentDto;
use App\Media\Entity\Moment;
use App\Media\Provider\MomentProvider;
use App\Media\Request\MomentSearchRequest;
use App\Media\ResponseMapper\MomentResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Moment')]
#[Route(path: '/moments')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly MomentProvider $momentProvider,
        private readonly MomentResponseMapper $momentResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: MomentSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'moments_find', methods: ['GET'])]
    public function find(MomentSearchRequest $searchRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, Moment::class);

        $searchRequest->userId = $this->getUser()->getId()->toString();

        $results = $this->momentProvider->search($searchRequest);
        $count = $this->momentProvider->count($searchRequest);

        $mappedResults = null !== $searchRequest->groupBy
            ? $this->momentResponseMapper->mapGroupedBy($results, $searchRequest->groupBy)
            : $this->momentResponseMapper->mapMultiple($results);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $mappedResults,
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MomentDto::class))
     )]
    #[Route(
        path: '/{momentId}',
        name: 'moments_get_one',
        requirements: [ 'momentId' => '%routing.uuid_mask%' ],
        methods: ['GET']
    )]
    public function getOne(#[OA\PathParameter] string $momentId): Response
    {
        $moment = $this->momentProvider->get(Uuid::fromString($momentId));

        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::READ, $moment);

        return new ApiJsonResponse(Response::HTTP_OK, $this->momentResponseMapper->map($moment));
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: MomentSearchRequest::class)),
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class))),
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(path: '/dates', name: 'moments_dates_find', methods: ['GET'])]
    public function searchDates(MomentSearchRequest $searchRequest): Response
    {
        $this->denyAccessUnlessGranted(AuthorizationVoterInterface::FIND, Moment::class);

        $searchRequest->userId = $this->getUser()->getId()->toString();
        $searchRequest->orderDirection = 'DESC';
        $searchRequest->orderProperty = 'recordedOn';

        if (null === $searchRequest->resultsPerPage) {
            $searchRequest->resultsPerPage = 100;
        }

        $results = $this->momentProvider->searchRecordedOnDates($searchRequest);
        $count = $this->momentProvider->countRecordedOnDates($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->momentResponseMapper->mapRecordedOnDates(
                $results,
                $searchRequest->expandMoments,
                $this->getUser()->getId(),
            ),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: MomentDto::class)))
    )]
    #[Route(
        path: '/dates/{recordedOn}',
        name: 'moments_search_by_date',
        methods: ['GET'],
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    public function searchByDate(#[OA\PathParameter] string $recordedOn, MomentSearchRequest $searchRequest): Response
    {
        $searchRequest->recordedOn = $recordedOn;

        if (null === $searchRequest->resultsPerPage) {
            $searchRequest->resultsPerPage = 100;
        }

        if (null === $searchRequest->orderDirection) {
            $searchRequest->orderDirection = 'ASC';
        }

        if (null === $searchRequest->orderProperty) {
            $searchRequest->orderProperty = 'recordedAt';
        }

        return $this->find($searchRequest);
    }
}
