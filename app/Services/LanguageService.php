<?php

namespace App\Services;

use App\Models\User;

class LanguageService
{
    public static function getSupportedLanguages(): array
    {
        return [
            'nl' => 'Nederlands',
            'en' => 'English',
        ];
    }

    public static function getDefaultLanguage(): string
    {
        return 'nl';
    }

    public static function getCurrentLanguage(): string
    {
        return auth()->check() 
            ? auth()->user()->language 
            : self::getDefaultLanguage();
    }

    public static function getLocalizedValue(array $values, ?string $language = null): string
    {
        $language = $language ?? self::getCurrentLanguage();
        $fallback = self::getDefaultLanguage();

        // Try to get the value in the requested language
        if (isset($values[$language]) && !empty($values[$language])) {
            return $values[$language];
        }

        // Fall back to the default language
        if (isset($values[$fallback]) && !empty($values[$fallback])) {
            return $values[$fallback];
        }

        // If no value is available, return the first non-empty value
        foreach ($values as $value) {
            if (!empty($value)) {
                return $value;
            }
        }

        return '';
    }

    public static function setUserLanguage(User $user, string $language): void
    {
        if (array_key_exists($language, self::getSupportedLanguages())) {
            $user->update(['language' => $language]);
        }
    }
} 