<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = [
        'bus_number', 
        'type', 
        'operator_name', 
        'status', 
        'total_seats',
        'driver_name',   // ← Add this
        'driver_phone',  // ← Add this
    ];
    
    public function routes()
    {
        return $this->belongsToMany(Route::class);
    }
    
    public function statusUpdates()
    {
        return $this->hasMany(BusStatusUpdate::class);
    }
    
    public function latestStatus()
    {
        return $this->hasOne(BusStatusUpdate::class)->latestOfMany();
    }
}