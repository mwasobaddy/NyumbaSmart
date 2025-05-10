<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyRenovation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'unit_id',
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'status',
        'notes',
        'document_paths',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'document_paths' => 'json',
    ];

    /**
     * Get the property that owns the renovation.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the unit that the renovation is for (optional).
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the user who created the renovation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendors assigned to the renovation.
     */
    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'renovation_vendor')
            ->withPivot([
                'service_provided', 
                'contracted_amount', 
                'paid_amount', 
                'contract_date', 
                'completion_date', 
                'status', 
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get the expenses for the renovation.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(RenovationExpense::class);
    }

    /**
     * Calculate the progress percentage of the renovation.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->status === 'planned') {
            return 0;
        }

        if ($this->status === 'completed') {
            return 100;
        }

        // Calculate based on current date relative to start and end dates
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        
        if ($startDate->isFuture()) {
            return 0;
        }

        $totalDays = $startDate->diffInDays($endDate) ?: 1; // Avoid division by zero
        $daysElapsed = $startDate->diffInDays(now());
        
        $percentage = ($daysElapsed / $totalDays) * 100;
        
        return min(99, max(0, $percentage)); // Cap at 99% for in_progress
    }

    /**
     * Calculate the budget percentage used.
     */
    public function getBudgetUsedPercentageAttribute(): float
    {
        if ($this->budget <= 0) {
            return 0;
        }
        
        return min(100, ($this->actual_cost / $this->budget) * 100);
    }

    /**
     * Check if the renovation is over budget.
     */
    public function getIsOverBudgetAttribute(): bool
    {
        return $this->actual_cost > $this->budget;
    }
    
    /**
     * Get the total amount contracted to vendors.
     */
    public function getTotalContractedAmountAttribute(): float
    {
        return $this->vendors->sum(function ($vendor) {
            return (float) $vendor->pivot->contracted_amount;
        });
    }
    
    /**
     * Get the total amount paid to vendors.
     */
    public function getTotalPaidAmountAttribute(): float
    {
        return $this->vendors->sum(function ($vendor) {
            return (float) $vendor->pivot->paid_amount;
        });
    }
}
