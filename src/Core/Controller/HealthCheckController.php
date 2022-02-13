<?php

namespace App\Core\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    #[Route(path: '/health/check', methods: ['GET'], name: 'health_check')]
    public function healthCheck(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
        ], Response::HTTP_OK);
    }
}
