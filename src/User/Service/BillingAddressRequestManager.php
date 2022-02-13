<?php

namespace App\User\Service;

use App\User\Entity\BillingAddress;
use App\User\Request\BillingAddressRequest;
use Ramsey\Uuid\Uuid;

class BillingAddressRequestManager
{
    private BillingAddressManager $billingAddressManager;

    public function __construct(
        BillingAddressManager $billingAddressManager
    ) {
        $this->billingAddressManager = $billingAddressManager;
    }

    public function createFromRequest(BillingAddressRequest $billingAddressRequest): BillingAddress
    {
        $billingAddress = new BillingAddress();

        $this->mapFromRequest($billingAddress, $billingAddressRequest);

        $this->billingAddressManager->create($billingAddress);

        return $billingAddress;
    }

    public function updateFromRequest(
        BillingAddress $billingAddress,
        BillingAddressRequest $billingAddressRequest
    ): void {
        $this->mapFromRequest($billingAddress, $billingAddressRequest);

        $this->billingAddressManager->update($billingAddress);
    }

    private function mapFromRequest(
        BillingAddress $billingAddress,
        BillingAddressRequest $billingAddressRequest
    ): void {
        if ($billingAddressRequest->has('userId')) {
            $billingAddress->setUserId(Uuid::fromString($billingAddressRequest->userId));
        }

        if ($billingAddressRequest->has('companyName')) {
            $billingAddress->setCompanyName($billingAddressRequest->companyName);
        }

        if ($billingAddressRequest->has('name')) {
            $billingAddress->setName($billingAddressRequest->name);
        }

        if ($billingAddressRequest->has('email')) {
            $billingAddress->setEmail($billingAddressRequest->email);
        }

        if ($billingAddressRequest->has('phoneIntlCode')) {
            $billingAddress->setPhoneIntlCode($billingAddressRequest->phoneIntlCode);
        }

        if ($billingAddressRequest->has('phoneAreaCode')) {
            $billingAddress->setPhoneAreaCode($billingAddressRequest->phoneAreaCode);
        }

        if ($billingAddressRequest->has('phoneNumber')) {
            $billingAddress->setPhoneNumber($billingAddressRequest->phoneNumber);
        }

        if ($billingAddressRequest->has('documentType')) {
            $billingAddress->setDocumentType($billingAddressRequest->documentType);
        }

        if ($billingAddressRequest->has('documentNumber')) {
            $billingAddress->setDocumentNumber($billingAddressRequest->documentNumber);
        }

        if ($billingAddressRequest->has('addressLine1')) {
            $billingAddress->setAddressLine1($billingAddressRequest->addressLine1);
        }

        if ($billingAddressRequest->has('addressLine2')) {
            $billingAddress->setAddressLine2($billingAddressRequest->addressLine2);
        }

        if ($billingAddressRequest->has('addressNumber')) {
            $billingAddress->setAddressNumber($billingAddressRequest->addressNumber);
        }

        if ($billingAddressRequest->has('addressDistrict')) {
            $billingAddress->setAddressDistrict($billingAddressRequest->addressDistrict);
        }

        if ($billingAddressRequest->has('addressCity')) {
            $billingAddress->setAddressCity($billingAddressRequest->addressCity);
        }

        if ($billingAddressRequest->has('addressState')) {
            $billingAddress->setAddressState($billingAddressRequest->addressState);
        }

        if ($billingAddressRequest->has('addressCountry')) {
            $billingAddress->setAddressCountry($billingAddressRequest->addressCountry);
        }

        if ($billingAddressRequest->has('addressZipCode')) {
            $billingAddress->setAddressZipCode($billingAddressRequest->addressZipCode);
        }
    }
}
