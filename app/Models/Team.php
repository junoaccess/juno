<?php

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use App\Models\Traits\ManagesTeamUsers;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(OrganizationScope::class)]
class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    use ManagesTeamUsers;
    use Sluggable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'organization_id',
    ];

    /**
     * Get the organization that owns the team.
     */
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the users that belong to the team.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(TeamUser::class)->withTimestamps();
    }

    /**
     * Get columns that should be used for unique slug constraint.
     */
    protected function sluggableUniqueColumns(): array
    {
        return ['organization_id'];
    }
}
