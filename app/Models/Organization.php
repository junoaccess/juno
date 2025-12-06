<?php

namespace App\Models;

use App\Models\Traits\ManagesOrganizationUsers;
use App\Models\Traits\SendsInvitations;
use App\Models\Traits\Sluggable;
use App\Observers\OrganizationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(OrganizationObserver::class)]
class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    use ManagesOrganizationUsers;
    use SendsInvitations;
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
        'email',
        'phone',
        'website',
        'owner_name',
        'owner_email',
        'owner_phone',
    ];

    /**
     * Get the teams for the organization.
     */
    public function teams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the roles for the organization.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get the invitations for the organization.
     */
    public function invitations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the users that belong to the organization.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(OrganizationUser::class)->withTimestamps();
    }
}
