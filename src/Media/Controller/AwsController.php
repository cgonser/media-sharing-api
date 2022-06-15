<?php

namespace App\Media\Controller;

use App\Core\Response\ApiJsonResponse;
use App\Media\Provider\MediaItemProvider;
use App\Media\Request\MomentRequest;
use App\Media\Service\MediaItemManager;
use DateTime;
use DateTimeInterface;
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
        private readonly MediaItemProvider $mediaItemProvider,
        private readonly MediaItemManager $mediaItemManager,
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
        $content = json_decode($request->getContent(), true);

        if (!isset($content['detail'])) {
            return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
        }

        $detail = $content['detail'];

        $this->logger->info('aws.media_convert.status_update', [
            'jobId' => $detail['jobId'],
            'status' => $detail['status'],
            'timestamp' => $content['time'],
        ]);

        $mediaItems = $this->mediaItemProvider->findBy(['awsJobId' => $detail['jobId']]);

        foreach ($mediaItems as $mediaItem) {
            $this->mediaItemManager->refreshStatus($mediaItem);
        }

        return new ApiJsonResponse(Response::HTTP_NO_CONTENT);
    }
}
