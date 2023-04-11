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
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Host extends Model implements Benchmarkable
{
    use HasFactory;
    use UsesTenantConnection;
    use HasBenchmarks;

    protected $fillable = [
        'hostname',
        'nb_kw_pos_1_10',
        'nb_kw_pos_11_20',
        'nb_kw_pos_21plus',
    ];

    public function getTitleAttribute(): string
    {
        return $this->attributes['hostname'];
    }

    public function getRouteKeyName()
    {
        return 'hostname';
    }

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'hosts_keywords')
            ->withPivot('rank', 'url_id')
            ->withTimestamps();
    }

    public function keywordsHosts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Host::class,
            HostKeyword::class,
            'host_id',
            'id',
            'id',
            'host_id',
        );
    }

    public function similars(): BelongsToMany
    {
        return $this->belongsToMany(Host::class, 'similar_hosts', 'host_id', 'similar_host_id')
            ->orderBy('score', 'desc')
            ->withPivot('score', 'lang')
            ->withTimestamps();
    }

    public function backlinkUrls(): HasMany
    {
        return $this->hasMany(Backlink::class);
    }
}
