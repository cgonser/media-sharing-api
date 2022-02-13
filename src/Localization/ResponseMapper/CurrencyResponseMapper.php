<?php

namespace App\Localization\ResponseMapper;

use App\Localization\Dto\CurrencyDto;
use App\Localization\Entity\Currency;

class CurrencyResponseMapper
{
    public function map(Currency $currency): CurrencyDto
    {
        $currencyDto = new CurrencyDto();
        $currencyDto->id = $currency->getId()->toString();
        $currencyDto->name = $currency->getName();
        $currencyDto->code = $currency->getCode();

        return $currencyDto;
    }

    public function mapMultiple(array $currencies): array
    {
        $currencyDtos = [];

        foreach ($currencies as $currency) {
            $currencyDtos[] = $this->map($currency);
        }

        return $currencyDtos;
    }
}
