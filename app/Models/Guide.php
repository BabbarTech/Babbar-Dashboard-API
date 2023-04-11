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
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Guide extends Model
{
    use HasFactory;
    use UsesTenantConnection;

    protected $fillable = [
        'keyword_id',
        'yourtextguru_guide_id',
        'lang',
        'status',
        'group_id',
        'grammes1',
        'grammes2',
        'grammes3',
        'entities',
    ];

    protected $casts = [
        //'lang' => SerpEnum::class,
        'status' => StatusEnum::class,
        'grammes1' => 'array',
        'grammes2' => 'array',
        'grammes3' => 'array',
        'entities' => 'array',
    ];

    public function serps(): BelongsToMany
    {
        return $this->belongsToMany(Url::class, 'guide_serps')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function checks(): HasMany
    {
        return $this->hasMany(GuideCheck::class);
    }

    public static function findByGuideId(int $yourTextGuruGuideId): Guide
    {
        return Guide::where('yourtextguru_guide_id', $yourTextGuruGuideId)->firstOrFail();
    }
}
