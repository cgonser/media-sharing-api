<?php

namespace App\Localization\Provider;

use App\Core\Provider\AbstractProvider;
use App\Localization\Entity\Currency;
use App\Localization\Repository\CurrencyRepository;

class CurrencyProvider extends AbstractProvider
{
    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByCode(string $code): ?Currency
    {
        return $this->repository->findOneBy(['code' => $code]);
    }

    public function getByCode(string $code): Currency
    {
        return $this->getBy(['code' => $code]);
    }
}
