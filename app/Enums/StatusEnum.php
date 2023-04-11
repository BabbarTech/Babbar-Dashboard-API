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

enum StatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case DONE = 'done';
    case ERROR = 'error';
    case CANCELLED = 'cancelled';
    case SKIPPED = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => ___('pending'),
            self::PROCESSING => ___('processing'),
            self::DONE => ___('done'),
            self::ERROR => ___('error'),
            self::CANCELLED => ___('cancelled'),
            self::SKIPPED => ___('skipped'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-light text-dark',
            self::PROCESSING => 'bg-primary',
            self::DONE => 'bg-success',
            self::ERROR => 'bg-danger',
            self::CANCELLED => 'bg-light text-dark',
            self::SKIPPED => 'bg-secondary',
        };
    }

    static public function allExceptPending(): array
    {
        $options = [];
        foreach (self::cases() as $value) {
            if ($value === self::PENDING) {
                continue;
            }

            $options[] = $value;
        }

        return $options;
    }
}
