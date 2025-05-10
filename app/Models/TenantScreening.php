<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantScreening extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'unit_id',
        'notes',
        'status',
        'credit_check_passed',
        'background_check_passed',
        'eviction_check_passed',
        'employment_verified',
        'income_verified',
        'document_path',
        'report_data',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credit_check_passed' => 'boolean',
        'background_check_passed' => 'boolean',
        'eviction_check_passed' => 'boolean',
        'employment_verified' => 'boolean',
        'income_verified' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the landlord associated with the screening.
     */
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the tenant associated with the screening.
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the unit associated with the screening.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Check if the screening is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the screening is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->isCompleted() && 
            $this->credit_check_passed && 
            $this->background_check_passed && 
            $this->eviction_check_passed;
    }

    /**
     * Get the status label for display.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }
}
