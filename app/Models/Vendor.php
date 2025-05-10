<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'business_type',
        'address',
        'description',
        'user_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who added the vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the renovations that this vendor is assigned to.
     */
    public function renovations(): BelongsToMany
    {
        return $this->belongsToMany(PropertyRenovation::class, 'renovation_vendor')
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
     * Get the expenses associated with this vendor.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(RenovationExpense::class);
    }

    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the vendor's pending payment amount across all renovations.
     */
    public function getPendingPaymentAmountAttribute(): float
    {
        return $this->renovations->sum(function ($renovation) {
            $contracted = (float) $renovation->pivot->contracted_amount;
            $paid = (float) $renovation->pivot->paid_amount;
            return max(0, $contracted - $paid);
        });
    }
    
    /**
     * Get the vendor's total contracted amount across all renovations.
     */
    public function getTotalContractedAmountAttribute(): float
    {
        return $this->renovations->sum(function ($renovation) {
            return (float) $renovation->pivot->contracted_amount;
        });
    }
    
    /**
     * Get the vendor's total paid amount across all renovations.
     */
    public function getTotalPaidAmountAttribute(): float
    {
        return $this->renovations->sum(function ($renovation) {
            return (float) $renovation->pivot->paid_amount;
        });
    }
}
