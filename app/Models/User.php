<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function vacateNotices()
    {
        return $this->hasMany(VacateNotice::class);
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the tenant screenings where this user is the tenant.
     */
    public function tenantScreenings()
    {
        return $this->hasMany(TenantScreening::class, 'tenant_id');
    }

    /**
     * Get the tenant screenings where this user is the landlord.
     */
    public function landlordScreenings()
    {
        return $this->hasMany(TenantScreening::class, 'landlord_id');
    }

    /**
     * Check if this user has completed their tenant profile.
     */
    public function hasCompletedProfile()
    {
        return $this->profile && $this->profile->isComplete();
    }

    /**
     * Check if this user can be screened (has submitted required information).
     */
    public function canBeScreened()
    {
        return $this->hasRole('Tenant') && $this->hasCompletedProfile();
    }
}
