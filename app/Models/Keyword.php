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

use App\Models\Contracts\Benchmarkable;
use App\Models\Traits\HasBenchmarks;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property string $keywords
 * @property string $lang
 */
class Keyword extends Model implements Benchmarkable
{
    use HasFactory;
    use UsesTenantConnection;
    use HasBenchmarks;

    protected $fillable = [
        'keywords',
        'lang',
        'bks',
        'nb_words',
    ];

    public function getTitleAttribute(): string
    {
        return $this->attributes['keywords'];
    }

    public function hosts(): BelongsToMany
    {
        return $this->belongsToMany(Host::class, 'hosts_keywords')
            ->orderBy('rank')
            ->withPivot('rank', 'url_id')
            ->withTimestamps();
    }

    /**
     * proxy method name
     */
    public function serp(): BelongsToMany
    {
        return $this->urls();
    }

    public function urls(): BelongsToMany
    {
        return $this->belongsToMany(Url::class, 'serps')
            ->withPivot('rank')
            ->withTimestamps();
    }

    public function guides(): HasMany
    {
        return $this->hasMany(Guide::class, 'keyword_id');
    }

    public function latestGuide(): HasOne
    {
        return $this->hasOne(Guide::class)->latestOfMany();
    }
}
