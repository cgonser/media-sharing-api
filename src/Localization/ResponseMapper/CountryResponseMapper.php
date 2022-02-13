<?php

namespace App\Localization\ResponseMapper;

use App\Localization\Dto\CountryDto;
use App\Localization\Entity\Country;

class CountryResponseMapper
{
    public function __construct(
        private CurrencyResponseMapper $currencyResponseMapper,
    ) {
    }

    public function map(Country $country): CountryDto
    {
        $countryDto = new CountryDto();
        $countryDto->code = $country->getCode();
        $countryDto->name = $country->getName();
        $countryDto->primaryLocale = $country->getPrimaryLocale();
        $countryDto->primaryTimezone = $country->getPrimaryTimezone();
        $countryDto->timezones = $country->getTimezones();
        $countryDto->isActive = $country->isActive();

        if (null !== $country->getCurrency()) {
            $countryDto->currency = $this->currencyResponseMapper->map($country->getCurrency());
        }

        return $countryDto;
    }

    public function mapCode(Country $country): CountryDto
    {
        $countryDto = new CountryDto();
        $countryDto->code = $country->getCode();
        $countryDto->name = $country->getName();

        return $countryDto;
    }

    public function mapMultiple(array $countries): array
    {
        $countryDtos = [];

        foreach ($countries as $country) {
            $countryDtos[] = $this->map($country);
        }

        return $countryDtos;
    }

    public function mapMultipleCodes(array $countries): array
    {
        $countryDtos = [];

        foreach ($countries as $country) {
            $countryDtos[] = $this->mapCode($country);
        }

        return $countryDtos;
    }
}
