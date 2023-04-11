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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Url extends Model
{
    use HasFactory;
    use UsesTenantConnection;

    protected $fillable = [
        'url',
        'host_id',
    ];

    protected static function booted(): void
    {
        static::saving(function ($url) {
            if (empty($url->host_id)) {
                $hostname = parse_url($url->url, PHP_URL_HOST);
                $host = Host::firstOrCreate(['hostname' => $hostname]);
                $url->host_id = $host->getKey();
            }
        });
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function overviews(): HasMany
    {
        return $this->hasMany(UrlOverview::class);
    }

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'hosts_keywords')
            ->withPivot('rank', 'url_id')
            ->withTimestamps();
    }

    public function backlinks(): HasMany
    {
        return $this->hasMany(Backlink::class, 'target_url_id');
    }

    public function latestOverview(): HasOne
    {
        return $this->hasOne(UrlOverview::class)
            ->latestOfMany('source_date');
    }

    public function pageAnalyzes(): HasMany
    {
        return $this->hasMany(PageAnalyze::class);
    }

    public function latestPageAnalyze(): HasOne
    {
        return $this->hasOne(PageAnalyze::class)->latestOfMany();
    }

    public function scopeTop10(Builder $query): void
    {
        $query->where('rank', '<=', 10);
    }
}
