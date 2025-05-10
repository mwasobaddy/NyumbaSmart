<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenovationExpense extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_renovation_id',
        'vendor_id',
        'expense_category',
        'title',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'receipt_number',
        'receipt_path',
        'added_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];
    
    /**
     * Get the renovation that owns the expense.
     */
    public function propertyRenovation(): BelongsTo
    {
        return $this->belongsTo(PropertyRenovation::class);
    }
    
    /**
     * Get the vendor associated with the expense.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    
    /**
     * Get the user who added the expense.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    
    /**
     * Scope a query to only include expenses within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
    
    /**
     * Scope a query to only include expenses of a specific category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('expense_category', $category);
    }
    
    /**
     * Check if the expense has a receipt.
     */
    public function getHasReceiptAttribute(): bool
    {
        return !empty($this->receipt_path);
    }
}
