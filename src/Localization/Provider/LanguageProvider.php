<?php

namespace App\Localization\Provider;

use App\Core\Provider\AbstractProvider;
use App\Localization\Entity\Language;
use App\Localization\Repository\LanguageRepository;

class LanguageProvider extends AbstractProvider
{
    public function __construct(LanguageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByCode(string $code): ?Language
    {
        return $this->repository->findOneBy(['code' => $code]);
    }

    public function getByCode(string $code): Language
    {
        return $this->getBy(['code' => $code]);
    }
}
