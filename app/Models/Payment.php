<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'invoice_id',
        'transaction_id',
        'amount',
        'phone',
        'payment_method',
        'status',
        'description',
        'payment_date',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];
    
    /**
     * Get the user that made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the invoice associated with the payment
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    
    /**
     * Scope a query to only include completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope a query to only include failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    /**
     * Scope a query to only include payments by a specific method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
    
    /**
     * Generate a downloadable receipt
     */
    public function generateReceipt()
    {
        // Implementation for generating a PDF receipt
        // This is a placeholder for the actual implementation
        return [
            'receipt_number' => $this->transaction_id,
            'amount' => $this->amount,
            'date' => $this->payment_date->format('Y-m-d H:i:s'),
            'user' => $this->user ? $this->user->name : 'Guest',
            'description' => $this->description,
            'payment_method' => $this->payment_method,
        ];
    }
}
