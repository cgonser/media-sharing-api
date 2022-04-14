<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\Moment;
use App\Media\Exception\MomentNotFoundException;
use App\Media\Repository\MomentRepository;
use Ramsey\Uuid\UuidInterface;

class MomentProvider extends AbstractProvider
{
    public function __construct(MomentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $userId, UuidInterface $momentId): Moment
    {
        /** @var Moment|null $moment */
        $moment = $this->repository->findOneBy([
            'id' => $momentId,
            'userId' => $userId,
        ]);

        if (!$moment) {
            throw new MomentNotFoundException();
        }

        return $moment;
    }

    protected function throwNotFoundException(): void
    {
        throw new MomentNotFoundException();
    }

    protected function getSearchableFields(): array
    {
        return [
            'location' => 'text',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'location',
            'mood',
        ];
    }
}
