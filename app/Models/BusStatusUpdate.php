<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusStatusUpdate extends Model
{
    protected $fillable = [
        'bus_id', 'route_id', 'user_id', 'status', 'current_stop', 
        'seats_available', 'eta_minutes', 'latitude', 'longitude', 
        'gps_accuracy', 'direction', 'update_time'
    ];
    
    protected $casts = [
        'update_time' => 'datetime',
    ];
    
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}