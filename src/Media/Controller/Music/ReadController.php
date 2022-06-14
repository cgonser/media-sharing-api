<?php

namespace App\Media\Controller\Music;

use App\Core\Response\ApiJsonResponse;
use App\Media\Dto\MusicDto;
use App\Media\Entity\Music;
use App\Media\Provider\MusicProvider;
use App\Media\Request\MusicSearchRequest;
use App\Media\ResponseMapper\MusicResponseMapper;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Music')]
#[Route(path: '/music')]
class ReadController extends AbstractController
{
    public function __construct(
        private readonly MusicProvider $musicProvider,
        private readonly MusicResponseMapper $musicResponseMapper,
    ) {
    }

    #[OA\Parameter(
        name: "filters",
        in: "query",
        schema: new OA\Schema(ref: new Model(type: MusicSearchRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: MusicDto::class)))
    )]
    #[ParamConverter(data: 'searchRequest', converter: 'querystring')]
    #[Route(name: 'musics_find', methods: ['GET'])]
    public function find(MusicSearchRequest $searchRequest): Response
    {
        $count = $this->musicProvider->count($searchRequest);
        $results = $this->musicProvider->search($searchRequest);

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->musicResponseMapper->mapMultiple($results),
            [
                'X-Total-Count' => $count,
            ]
        );
    }

    #[OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(ref: new Model(type: MusicDto::class))
     )]
    #[Route(path: '/{musicId}', name: 'musics_get_one', methods: ['GET'])]
    public function getOne(#[OA\PathParameter] string $musicId): Response
    {
        /** @var Music $music */
        $music = $this->musicProvider->get(Uuid::fromString($musicId));

        return new ApiJsonResponse(
            Response::HTTP_OK,
            $this->musicResponseMapper->map($music)
        );
    }
}
