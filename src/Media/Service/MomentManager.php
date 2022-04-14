<?php

namespace App\Media\Service;

use App\Core\Validation\EntityValidator;
use App\Media\Entity\Moment;
use App\Media\Repository\MomentRepository;

class MomentManager
{
    public function __construct(
        private MomentRepository $momentRepository,
        private EntityValidator $validator,
    ) {
    }

    public function create(Moment $moment): void
    {
        $this->save($moment);
    }

    public function update(Moment $moment): void
    {
        $this->save($moment);
    }

    public function delete(object $moment): void
    {
        $this->momentRepository->delete($moment);
    }

    public function save(Moment $moment): void
    {
        $this->validator->validate($moment);

        $this->momentRepository->save($moment);
    }
}
