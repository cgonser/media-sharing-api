<?php

namespace App\User\Service;

use App\User\Entity\BillingAddress;
use App\User\Repository\BillingAddressRepository;

class BillingAddressManager
{
    private BillingAddressRepository $billingAddressRepository;

    public function __construct(
        BillingAddressRepository $billingAddressRepository
    ) {
        $this->billingAddressRepository = $billingAddressRepository;
    }

    public function create(BillingAddress $billingAddress): void
    {
        $this->billingAddressRepository->save($billingAddress);
    }

    public function update(BillingAddress $billingAddress): void
    {
        $this->billingAddressRepository->save($billingAddress);
    }

    public function delete(BillingAddress $billingAddress): void
    {
        $this->billingAddressRepository->delete($billingAddress);
    }
}
