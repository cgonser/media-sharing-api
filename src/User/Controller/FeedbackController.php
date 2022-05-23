<?php

namespace App\User\Controller;

use App\Core\Response\ApiJsonResponse;
use App\User\Request\UserFeedbackRequest;
use App\User\Service\UserEmailManager;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'User')]
#[Route(path: '/users')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly UserEmailManager $userEmailManager,
    ) {
    }

    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: new Model(type: UserFeedbackRequest::class)))]
    #[OA\Response(response: 204, description: "Success")]
    #[ParamConverter(
        data: 'userFeedbackRequest',
        options: ['deserializationContext' => ['allow_extra_attributes' => false]],
        converter: 'fos_rest.request_body'
    )]
    #[Route(path: '/current/feedback', name: 'users_feedback_send', methods: ['POST'])]
    public function sendFeedback(UserFeedbackRequest $userFeedbackRequest): Response
    {
        $this->userEmailManager->sendFeedbackEmail(
            $this->getUser(),
            $userFeedbackRequest
        );

        return new ApiJsonResponse(
            Response::HTTP_NO_CONTENT,
        );
    }
}
