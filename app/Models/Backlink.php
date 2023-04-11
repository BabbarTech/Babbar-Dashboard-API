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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property Url $sourceUrl
 * @property Url $targetUrl
 */
class Backlink extends Model
{
    use HasFactory;
    use UsesTenantConnection;

    protected $fillable = [
        'host_id',
        'source_url_id',
        'target_url_id',
        'induced_strength',
        'induced_strength_confidence',
        'link_type',
        'link_text',
        'link_rels',
        'language',
        'ip',
    ];

    public function sourceUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'source_url_id');
    }

    public function targetUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'target_url_id');
    }

    public function sourceUrlOverviews(): HasMany
    {
        return $this->hasMany(UrlOverview::class, 'url_id', 'source_url_id');
    }

    public function latestSourceUrlOverview(): HasOne
    {
        return $this->hasOne(UrlOverview::class, 'url_id', 'source_url_id')
            ->latestOfMany('source_date');
    }

    public function targetUrlOverviews(): HasMany
    {
        return $this->hasMany(UrlOverview::class, 'url_id', 'target_url_id');
    }

    public function latestTargetUrlOverview(): HasOne
    {
        return $this->hasOne(UrlOverview::class, 'url_id', 'target_url_id')
            ->latestOfMany('source_date');
    }
}
