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

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use UsesLandlordConnection;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', true);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function gateways(): HasMany
    {
        return $this->hasMany(Gateway::class, 'admin_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getBabbarApiKey(): ?string
    {
        return $this->getBabbarApiAccount()?->api_token;
    }

    public function getBabbarApiAccount(): ?Gateway
    {
        // TODO : améliorer ça
        return Gateway::where('name', Gateway::BABBAR)->first();
    }

    public function getYourTextGuruApiAccount(): ?Gateway
    {
        // TODO : améliorer ça
        return Gateway::where('name', Gateway::YTG)->first();
    }

    public function getGatewayApiAccount(string $gatewayKey): ?Gateway
    {
        // TODO : améliorer ça
        return Gateway::where('name', $gatewayKey)->firstOrFail();
    }
}
