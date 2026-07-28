<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['name', 'origin', 'destination', 'direction', 'is_active', 'note'];
    
    public function stops()
    {
        return $this->hasMany(Stop::class)->orderBy('sequence');
    }
    
    public function buses()
    {
        return $this->belongsToMany(Bus::class);
    }
    
    public function statusUpdates()
    {
        return $this->hasMany(BusStatusUpdate::class);
    }
}