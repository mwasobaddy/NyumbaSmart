<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'employment_status',
        'employer_name',
        'monthly_income',
        'id_document_path',
        'income_document_path',
        'references',
        'additional_info',
        'application_submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'monthly_income' => 'decimal:2',
        'application_submitted_at' => 'datetime',
    ];

    /**
     * Get the user that owns this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the user profile is complete with required information.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        // Check if all required fields for tenant screening are filled
        return !empty($this->employment_status)
            && !empty($this->monthly_income)
            && !empty($this->id_document_path)
            && !empty($this->income_document_path)
            && !empty($this->application_submitted_at);
    }
}