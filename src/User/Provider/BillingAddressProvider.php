<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\BillingAddress;
use App\User\Exception\BillingAddressNotFoundException;
use App\User\Repository\BillingAddressRepository;
use Ramsey\Uuid\UuidInterface;

class BillingAddressProvider extends AbstractProvider
{
    public function __construct(BillingAddressRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $userId, UuidInterface $billingAddressId): BillingAddress
    {
        /** @var BillingAddress|null $billingAddress */
        $billingAddress = $this->repository->findOneBy([
            'id' => $billingAddressId,
            'userId' => $userId,
        ]);

        if (!$billingAddress) {
            throw new BillingAddressNotFoundException();
        }

        return $billingAddress;
    }

    public function findUserLatest(UuidInterface $userId): ?BillingAddress
    {
        return $this->findOneBy([
            'userId' => $userId,
        ], [
            'updatedAt' => 'DESC',
        ]);
    }

    protected function throwNotFoundException()
    {
        throw new BillingAddressNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
        ];
    }
}
