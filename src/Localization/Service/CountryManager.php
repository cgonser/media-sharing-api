<?php

namespace App\Localization\Service;

use App\Core\Validation\EntityValidator;
use App\Localization\Entity\Country;
use App\Localization\Repository\CountryRepository;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Timezones;

class CountryManager
{
    public function __construct(
        private CountryRepository $countryRepository,
        private EntityValidator $validator
    ) {
    }

    public function importCountries(): void
    {
        $countries = Countries::getNames();

        array_map(function ($code, $name) {
            $this->importCountry($code, $name);
        }, array_keys($countries), $countries);
    }

    private function importCountry(string $code, string $name): void
    {
        $country = $this->countryRepository->findOneBy(['code' => $code]);

        if (!$country) {
            $country = (new Country())
                ->setCode($code)
                ->setName($name);
        }

        if (null === $country->getTimezones() || 0 === count($country->getTimezones())) {
            $country->setTimezones(
                Timezones::forCountryCode($country->getCode())
            );
        }

        if (null === $country->getPrimaryTimezone() && 0 < count($country->getTimezones())) {
            $country->setPrimaryTimezone(
                $country->getTimezones()[0]
            );
        }

        $this->countryRepository->save($country);
    }

    public function create(Country $country): void
    {
        $this->validator->validate($country);

        $this->countryRepository->save($country);
    }

    public function update(Country $country): void
    {
        $this->validator->validate($country);

        $this->countryRepository->save($country);
    }
}
