<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','address','description','logo_url','theme_color','app_name'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get the renovations for this property.
     */
    public function renovations(): HasMany
    {
        return $this->hasMany(PropertyRenovation::class);
    }
}
