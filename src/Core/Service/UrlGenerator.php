<?php

namespace App\Core\Service;

use InvalidArgumentException;

class UrlGenerator
{
    public function __construct(
        private readonly string $frontendHost,
        private readonly array $urls,
    ) {
    }

    public function generate(string $identifier, array $placeholders = []): string
    {
        if (!isset($this->urls[$identifier])) {
            throw new InvalidArgumentException("Url not found");
        }

        foreach ($placeholders as $key => $value) {
            $placeholders['<'.$key.'>'] = $value;

            unset($placeholders[$key]);
        }

        return $this->frontendHost . strtr($this->urls[$identifier], $placeholders);
    }
}