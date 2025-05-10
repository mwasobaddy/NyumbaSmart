<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','unit_number','rent','manual_water','manual_electricity','status'];

    public function property()
    {
        return $this->belongsTo(Property::class);
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
     * Get the renovations specific to this unit.
     */
    public function renovations(): HasMany
    {
        return $this->hasMany(PropertyRenovation::class);
    }
}
