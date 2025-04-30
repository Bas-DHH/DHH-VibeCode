<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class LanguageService
{
    private const CACHE_KEY = 'supported_languages';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get supported languages with caching
     */
    public static function getSupportedLanguages(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return [
                'nl' => 'Nederlands',
                'en' => 'English',
            ];
        });
    }

    /**
     * Get the default language
     */
    public static function getDefaultLanguage(): string
    {
        return 'nl';
    }

    /**
     * Get the current language for the authenticated user
     */
    public static function getCurrentLanguage(): string
    {
        return Auth::check() 
            ? Auth::user()->language 
            : self::getDefaultLanguage();
    }

    /**
     * Get localized value with fallback support
     */
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

    /**
     * Set user language with validation
     */
    public static function setUserLanguage(User $user, string $language): void
    {
        if (array_key_exists($language, self::getSupportedLanguages())) {
            $user->update(['language' => $language]);
            Cache::forget("user_{$user->id}_language");
        }
    }

    /**
     * Get user language with caching
     */
    public static function getUserLanguage(User $user): string
    {
        return Cache::remember(
            "user_{$user->id}_language",
            self::CACHE_TTL,
            fn () => $user->language ?? self::getDefaultLanguage()
        );
    }

    /**
     * Clear language cache for a user
     */
    public static function clearUserLanguageCache(User $user): void
    {
        Cache::forget("user_{$user->id}_language");
    }
} 