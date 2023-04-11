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

namespace App\Models;

use App\Casts\EncryptedAndMasked;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

/**
 * @property int $x_ratelimit_remaining
 * @property int $x_ratelimit_limit
 * @property string $api_token
 */
class Gateway extends Model
{
    use HasFactory;
    use UsesLandlordConnection;
    use SoftDeletes;

    public const BABBAR = 'Babbar';
    public const YTG = 'YourTextGuru';
    public const TRAFILATURA = 'Trafilatura';

    protected $fillable = [
        'name',
        'api_token',
        'x_ratelimit_limit',
        'x_ratelimit_remaining',
    ];

    protected $casts = [
        'api_token' => EncryptedAndMasked::class,
    ];

    protected $hidden = [
        'api_token',
    ];

    protected function maskedApiToken(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                /** @var array $attributes */
                return EncryptedAndMasked::mask($attributes['api_token']);
            }
        );
    }

    public function getDefaultRateLimitPerMinute(int $default = 6): int
    {
        $limit = config('gateways.' . $this->name . '.default_rate_limit_per_minute', $default);

        if (! is_numeric($limit)) {
            throw new \Exception('default_rate_limit_per_minute must be an integer');
        }

        return (int) $limit;
    }

    public function getRemainingRateLimitPerMinute(): int
    {
        /*
        $remainingCall = $this->updated_at?->isSameMinute(now()) ?
            $this->x_ratelimit_remaining : $this->x_ratelimit_limit;

        return $remainingCall ?:
            $this->getDefaultRateLimitPerMinute();
        */

        return $this->x_ratelimit_limit ?? $this->getDefaultRateLimitPerMinute();
    }
}
