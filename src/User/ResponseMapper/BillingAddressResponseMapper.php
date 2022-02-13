<?php

namespace App\User\ResponseMapper;

use App\User\Dto\BillingAddressDto;
use App\User\Entity\BillingAddress;

class BillingAddressResponseMapper
{
    public function map(BillingAddress $billingAddress): BillingAddressDto
    {
        $billingAddressDto = new BillingAddressDto();
        $billingAddressDto->id = $billingAddress->getId();
        $billingAddressDto->userId = $billingAddress->getUserId();
        $billingAddressDto->companyName = $billingAddress->getCompanyName();
        $billingAddressDto->name = $billingAddress->getName();
        $billingAddressDto->email = $billingAddress->getEmail();
        $billingAddressDto->phoneIntlCode = $billingAddress->getPhoneIntlCode();
        $billingAddressDto->phoneAreaCode = $billingAddress->getPhoneAreaCode();
        $billingAddressDto->phoneNumber = $billingAddress->getPhoneNumber();
        $billingAddressDto->documentType = $billingAddress->getDocumentType();
        $billingAddressDto->documentNumber = $billingAddress->getDocumentNumber();
        $billingAddressDto->addressLine1 = $billingAddress->getAddressLine1();
        $billingAddressDto->addressLine2 = $billingAddress->getAddressLine2();
        $billingAddressDto->addressNumber = $billingAddress->getAddressNumber();
        $billingAddressDto->addressDistrict = $billingAddress->getAddressDistrict();
        $billingAddressDto->addressCity = $billingAddress->getAddressCity();
        $billingAddressDto->addressState = $billingAddress->getAddressState();
        $billingAddressDto->addressCountry = $billingAddress->getAddressCountry();
        $billingAddressDto->addressZipCode = $billingAddress->getAddressZipCode();

        return $billingAddressDto;
    }

    public function mapMultiple(array $billingAddressEntries): array
    {
        $billingAddressDtos = [];

        foreach ($billingAddressEntries as $billingAddress) {
            $billingAddressDtos[] = $this->map($billingAddress);
        }

        return $billingAddressDtos;
    }
}
