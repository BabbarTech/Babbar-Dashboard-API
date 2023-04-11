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

use App\Enums\SerpEnum;
use App\Models\Contracts\Benchmarkable;
use App\Models\Traits\HasBenchmarks;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;
use Spatie\Multitenancy\Models\Tenant;

/**
 * @property SerpEnum $serp
 * @property User $user
 * @property Host $host
 * @property int $host_id
 * @property string $url
 * @property string $database
 * @property string $tenant_key
 * @property string $name
 */
class Project extends Tenant implements Benchmarkable
{
    use HasFactory;
    //use SoftDeletes;
    use UsesLandlordConnection;
    use HasBenchmarks;

    protected $fillable = [
        'url',
        'serp',
        'description',
    ];

    protected $casts = [
        'serp' => SerpEnum::class,
    ];

    public function getTitleAttribute(): string
    {
        return $this->attributes['hostname'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function keywords(): BelongsToMany
    {
        return $this->host->keywords();
    }

    public function getLang(): string
    {
        return $this->serp->locale();
    }

    public function getYourTextGuruLang(): string
    {
        return $this->serp->yourTextGuruLang();
    }

    public function getCountry(): string
    {
        return $this->serp->countryIsoCode();
    }

    public function getNameAttribute(): string
    {
        return $this->hostname . ' (' . $this->created_at?->format('Y-m-d H:i') . ')';
    }

    public function makeTenantKey(): string
    {
        return implode('_', [
            $this->hostname,
            date('Ymd'),
            $this->serp->value,
        ]);
    }
}
