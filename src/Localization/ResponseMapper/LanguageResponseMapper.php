<?php

namespace App\Localization\ResponseMapper;

use App\Localization\Dto\LanguageDto;
use App\Localization\Entity\Language;

class LanguageResponseMapper
{
    public function map(Language $language): LanguageDto
    {
        $languageDto = new LanguageDto();
        $languageDto->id = $language->getId()->toString();
        $languageDto->name = $language->getName();
        $languageDto->code = $language->getCode();

        return $languageDto;
    }

    public function mapMultiple(array $currencies): array
    {
        $languageDtos = [];

        foreach ($currencies as $language) {
            $languageDtos[] = $this->map($language);
        }

        return $languageDtos;
    }
}
