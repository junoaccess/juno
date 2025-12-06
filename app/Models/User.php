<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Scopes\UserOrganizationScope;
use App\Models\Traits\BelongsToOrganizations;
use App\Observers\UserObserver;
use Filterable\Traits\Filterable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[ObservedBy(UserObserver::class)]
#[ScopedBy(UserOrganizationScope::class)]
class User extends Authenticatable
{
    use BelongsToOrganizations;
    use Filterable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uid',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'email',
        'date_of_birth',
        'password',
        'email_verified_at',
        'current_organization_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'name',
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the user's current organization.
     */
    public function currentOrganization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    /**
     * Get the organizations that the user belongs to.
     */
    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->using(OrganizationUser::class)
            ->withPivot(['is_default'])
            ->withTimestamps();
    }

    /**
     * Get the teams that the user belongs to.
     */
    public function teams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Team::class)->using(TeamUser::class)->withTimestamps();
    }

    /**
     * Get the roles assigned to the user.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class)->withPivot('organization_id')->withTimestamps();
    }

    /**
     * Get the invitations sent by this user.
     */
    public function sentInvitations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Get the invitations received by this user.
     */
    public function receivedInvitations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invitation::class, 'email', 'email');
    }

    /**
     * Check if user has a permission in their current organization.
     */
    public function hasPermission(string $permission): bool
    {
        if (! $this->current_organization_id) {
            return false;
        }

        return $this->roles()
            ->where('organization_id', $this->current_organization_id)
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission)
                    ->orWhere('name', explode(':', $permission)[0].':*')
                    ->orWhere('name', '*');
            })
            ->exists();
    }

    /**
     * Check if user has a role in their current organization.
     */
    public function hasRole(string $role): bool
    {
        if (! $this->current_organization_id) {
            return false;
        }

        return $this->roles()
            ->where('organization_id', $this->current_organization_id)
            ->where('name', $role)
            ->exists();
    }

    /**
     * Set the current organization for this user.
     */
    public function setCurrentOrganization(Organization $organization): void
    {
        $this->update(['current_organization_id' => $organization->id]);
    }

    /**
     * Get the user's full name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->middle_name} {$this->last_name}")
        );
    }

    /**
     * Get the user's profile photo URL.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile_photo_path
                ? asset('storage/'.$this->profile_photo_path)
                : $this->defaultProfilePhotoUrl()
        );
    }

    /**
     * Get the default profile photo URL.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=6366F1';
    }
}
