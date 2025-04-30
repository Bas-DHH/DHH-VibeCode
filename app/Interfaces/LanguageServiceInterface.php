<?php

namespace App\Interfaces;

use App\Models\User;

interface LanguageServiceInterface
{
    public static function getSupportedLanguages(): array;
    public static function getDefaultLanguage(): string;
    public static function getCurrentLanguage(): string;
    public static function getLocalizedValue(array $values, ?string $language = null): string;
    public static function setUserLanguage(User $user, string $language): void;
    public static function getUserLanguage(User $user): string;
    public static function clearUserLanguageCache(User $user): void;
} 