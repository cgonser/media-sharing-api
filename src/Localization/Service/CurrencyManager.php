<?php

namespace App\Localization\Service;

use App\Core\Validation\EntityValidator;
use App\Localization\Entity\Currency;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Repository\CurrencyRepository;

class CurrencyManager
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private EntityValidator $entityValidator
    ) {
    }

    public function create(Currency $currency): void
    {
        $this->entityValidator->validate($currency);

        $this->currencyRepository->save($currency);
    }

    public function update(Currency $currency): void
    {
        $this->entityValidator->validate($currency);

        $this->currencyRepository->save($currency);
    }

    public function delete(Currency $currency): void
    {
        $this->currencyRepository->delete($currency);
    }
}
