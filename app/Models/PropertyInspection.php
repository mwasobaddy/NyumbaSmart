<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyInspection extends Model
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
        'type',
        'inspection_date',
        'status',
        'checklist_items',
        'overall_condition',
        'notes',
        'image_paths',
        'tenant_signed',
        'landlord_signed',
        'tenant_signed_at',
        'landlord_signed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'inspection_date' => 'date',
        'checklist_items' => 'json',
        'image_paths' => 'json',
        'tenant_signed' => 'boolean',
        'landlord_signed' => 'boolean',
        'tenant_signed_at' => 'datetime',
        'landlord_signed_at' => 'datetime',
    ];

    /**
     * Get the unit that the inspection is for.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the tenant associated with the inspection.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the landlord associated with the inspection.
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Check if the inspection is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the inspection is fully signed.
     */
    public function isFullySigned(): bool
    {
        return $this->tenant_signed && $this->landlord_signed;
    }
}
