<?php

namespace Upcoach\UpstartForLaravel\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $organization_id
 * @property string $token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Installation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['organization_id', 'token'];

    protected $casts = ['token' => 'encrypted'];

    public function scopeForOrganization(Builder $builder, string $organizationId): Builder
    {
        return $builder->where('organization_id', $organizationId);
    }

    public static function new(string $organizationId, string $token): Installation
    {
        return self::create([
            'organization_id' => $organizationId,
            'token' => $token,
        ]);
    }
}
