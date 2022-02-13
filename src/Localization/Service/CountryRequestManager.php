<?php

namespace App\Localization\Service;

use App\Localization\Entity\Country;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Request\CountryRequest;
use Ramsey\Uuid\Uuid;

class CountryRequestManager
{
    public function __construct(
        private CountryManager $countryManager,
        private CurrencyProvider $currencyProvider,
    ) {
    }

    public function createFromRequest(CountryRequest $countryRequest): Country
    {
        $country = new Country();

        $this->mapFromRequest($country, $countryRequest);

        $this->countryManager->create($country);

        return $country;
    }

    public function updateFromRequest(Country $country, CountryRequest $countryRequest): void
    {
        $this->mapFromRequest($country, $countryRequest);

        $this->countryManager->update($country);
    }

    private function mapFromRequest(Country $country, CountryRequest $countryRequest): void
    {
        if ($countryRequest->has('currencyId')) {
            $country->setCurrency(
                $this->currencyProvider->get(Uuid::fromString($countryRequest->currencyId))
            );
        }

        if ($countryRequest->has('code')) {
            $country->setCode($countryRequest->code);
        }

        if ($countryRequest->has('name')) {
            $country->setName($countryRequest->name);
        }

        if ($countryRequest->has('primaryTimezone')) {
            $country->setPrimaryTimezone($countryRequest->primaryTimezone);
        }

        if ($countryRequest->has('primaryLocale')) {
            $country->setPrimaryLocale($countryRequest->primaryLocale);
        }

        if ($countryRequest->has('isActive')) {
            $country->setIsActive($countryRequest->isActive);
        }
    }
}
