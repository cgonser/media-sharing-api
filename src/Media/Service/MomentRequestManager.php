<?php

namespace App\Media\Service;

use App\Media\Entity\Moment;
use App\Media\Request\MomentRequest;
use App\User\Provider\UserProvider;
use DateTime;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;

class MomentRequestManager
{
    public function __construct(
        private readonly MomentManager $momentManager,
        private readonly UserProvider $userProvider,
        private readonly LocationRequestManager $locationRequestManager,
    ) {
    }

    public function createFromRequest(MomentRequest $momentRequest): Moment
    {
        $moment = new Moment();

        $this->mapFromRequest($moment, $momentRequest);

        $this->momentManager->create($moment);

        return $moment;
    }

    public function updateFromRequest(Moment $moment, MomentRequest $momentRequest): void
    {
        $this->mapFromRequest($moment, $momentRequest);

        $this->momentManager->update($moment);
    }

    public function mapFromRequest(Moment $moment, MomentRequest $momentRequest): void
    {
        if ($momentRequest->has('userId')) {
            $moment->setUser(
                $this->userProvider->get(Uuid::fromString($momentRequest->userId))
            );
        }

        if ($momentRequest->has('mood')) {
            $moment->setMood($momentRequest->mood);
        }

        if ($momentRequest->has('recordedAt')) {
            $moment->setRecordedAt(
                DateTime::createFromFormat(DateTimeInterface::ATOM, $momentRequest->recordedAt)
            );
        }

        if ($momentRequest->has('location')) {
            $moment->setLocation(
                $momentRequest->location !== null
                    ? $this->locationRequestManager->createFromRequest($momentRequest->location)
                    : null
            );
        }
    }
}
