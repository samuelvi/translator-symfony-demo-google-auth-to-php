<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use Symfony\Contracts\Translation\TranslatorInterface;

final class TestTranslator implements TranslatorInterface
{
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return 'Translated text';
    }

    public function getLocale(): string
    {
        return 'en';
    }

    public function setFallbackLocales(array $locales): void
    {
        // Test stub - does nothing
    }
}
