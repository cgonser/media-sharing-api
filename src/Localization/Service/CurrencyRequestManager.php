<?php

namespace App\Localization\Service;

use App\Localization\Entity\Currency;
use App\Localization\Request\CurrencyRequest;

class CurrencyRequestManager
{
    public function __construct(
        private CurrencyManager $currencyManager
    ) {
    }

    public function createFromRequest(CurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency();

        $this->mapFromRequest($currency, $currencyRequest);

        $this->currencyManager->create($currency);

        return $currency;
    }

    public function updateFromRequest(Currency $currency, CurrencyRequest $currencyRequest): void
    {
        $this->mapFromRequest($currency, $currencyRequest);

        $this->currencyManager->update($currency);
    }

    public function mapFromRequest(Currency $currency, CurrencyRequest $currencyRequest): void
    {
        if ($currencyRequest->has('name')) {
            $currency->setName($currencyRequest->name);
        }

        if ($currencyRequest->has('code')) {
            $currency->setCode($currencyRequest->code);
        }
    }
}
