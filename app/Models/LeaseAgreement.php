<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseAgreement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_id',
        'tenant_id',
        'landlord_id',
        'start_date',
        'end_date',
        'rent_amount',
        'security_deposit',
        'terms_and_conditions',
        'special_provisions',
        'document_path',
        'status',
        'signed_by_tenant_at',
        'signed_by_landlord_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'signed_by_tenant_at' => 'datetime',
        'signed_by_landlord_at' => 'datetime',
    ];

    /**
     * Get the unit that the lease agreement belongs to.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the tenant who signed the lease agreement.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the landlord who signed the lease agreement.
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Check if the lease agreement is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the lease agreement is fully signed.
     */
    public function isFullySigned(): bool
    {
        return $this->signed_by_tenant_at !== null && $this->signed_by_landlord_at !== null;
    }
}
