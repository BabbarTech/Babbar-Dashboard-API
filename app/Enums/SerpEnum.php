<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\Enums;

use App\Enums\Traits\HasDropDownOptionTrait;

enum SerpEnum: string
{
    use HasDropDownOptionTrait;

    case fr_FR = 'fr_FR';
    case en_GB = 'en_GB';
    case es_ES = 'es_ES';

    public function label(): string
    {
        return match ($this) {
            self::fr_FR => 'Google FR',
            self::en_GB => 'Google GB',
            self::es_ES => 'Google ES',
        };
    }

    public function locale(): string
    {
        return match ($this) {
            self::fr_FR => 'fr',
            self::en_GB => 'en',
            self::es_ES => 'es',
        };
    }

    public function countryIsoCode(): string
    {
        return match ($this) {
            self::fr_FR => 'FR',
            self::en_GB => 'GB',
            self::es_ES => 'ES',
        };
    }

    public function yourTextGuruLang(): string{
        return match ($this) {
            self::fr_FR => 'fr_fr',
            self::en_GB => 'en_gb',
            self::es_ES => 'es_es',
        };
    }
}
