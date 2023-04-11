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

enum InducedStrengthConfidenceEnum: string
{
    use HasDropDownOptionTrait;

    case HIGH = 'HIGH';
    case LOW = 'LOW';
    case NONE = 'NONE';

    public function label(): string
    {
        return match ($this) {
            self::HIGH => ___('induced_strength_confidence.high'),
            self::LOW => ___('induced_strength_confidence.low'),
            self::NONE => ___('induced_strength_confidence.none'),
        };
    }
}
