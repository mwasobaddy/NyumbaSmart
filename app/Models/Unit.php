<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
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
}
