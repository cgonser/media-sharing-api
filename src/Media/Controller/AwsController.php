<?php

namespace App\Media\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Media\Request\MomentRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Media Item / Status Update')]
#[Route(path: 'public/aws')]
class AwsController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: MomentRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[OA\Response(response: 400, description: "Invalid input")]
    #[Route(path: '/media_convert/status', name: 'aws_media_convert_status_update', methods: ['POST', 'PATCH', 'PUT'])]
    public function mediaConvertUpdateStatus(
        Request $request,
    ): Response {
        $this->logger->info('aws.media_convert.status_update', [
            'request' => $request->getContent(),
        ]);

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
