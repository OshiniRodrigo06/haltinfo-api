<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Bus;

class RouteDataSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // FORWARD ROUTE: Panadura → Maharagama
        // ============================================
        
        $forwardRoute = Route::create([
            'name' => 'Panadura → Maharagama',
            'origin' => 'Panadura',
            'destination' => 'Maharagama',
            'direction' => 'forward',
            'is_active' => true,
            'note' => '4 stops · via Kottawa Exit',
        ]);
        
        $forwardStops = [
            ['name' => 'Panadura', 'sequence' => 1, 'type' => 'origin', 'latitude' => 6.7133, 'longitude' => 79.9037],
            ['name' => 'Galanigama', 'sequence' => 2, 'type' => 'mid', 'latitude' => 6.7580, 'longitude' => 79.9510],
            ['name' => 'Kottawa Exit', 'sequence' => 3, 'type' => 'mid', 'latitude' => 6.8050, 'longitude' => 79.9750],
            ['name' => 'Maharagama', 'sequence' => 4, 'type' => 'dest', 'latitude' => 6.8478, 'longitude' => 79.9271],
        ];
        
        foreach ($forwardStops as $stop) {
            Stop::create([
                'route_id' => $forwardRoute->id,
                'name' => $stop['name'],
                'sequence' => $stop['sequence'],
                'type' => $stop['type'],
                'latitude' => $stop['latitude'],
                'longitude' => $stop['longitude'],
            ]);
        }
        
        // ============================================
        // REVERSE ROUTE: Maharagama → Panadura
        // ============================================
        
        $reverseRoute = Route::create([
            'name' => 'Maharagama → Panadura',
            'origin' => 'Maharagama',
            'destination' => 'Panadura',
            'direction' => 'reverse',
            'is_active' => true,
            'note' => 'Uses Kottawa Entrance ramp (southbound)',
        ]);
        
        $reverseStops = [
            ['name' => 'Maharagama', 'sequence' => 1, 'type' => 'origin', 'latitude' => 6.8478, 'longitude' => 79.9271],
            ['name' => 'Kottawa Entrance', 'sequence' => 2, 'type' => 'mid', 'latitude' => 6.8012, 'longitude' => 79.9720],
            ['name' => 'Galanigama Exit', 'sequence' => 3, 'type' => 'mid', 'latitude' => 6.7560, 'longitude' => 79.9490],
            ['name' => 'Panadura', 'sequence' => 4, 'type' => 'dest', 'latitude' => 6.7133, 'longitude' => 79.9037],
        ];
        
        foreach ($reverseStops as $stop) {
            Stop::create([
                'route_id' => $reverseRoute->id,
                'name' => $stop['name'],
                'sequence' => $stop['sequence'],
                'type' => $stop['type'],
                'latitude' => $stop['latitude'],
                'longitude' => $stop['longitude'],
            ]);
        }
        
        // ============================================
        // CREATE BUSES
        // ============================================
        
        $buses = [
            ['bus_number' => 'NB-2341', 'type' => 'Luxury', 'operator_name' => 'RL Express', 'total_seats' => 50],
            ['bus_number' => 'NB-5589', 'type' => 'Semi-Luxury', 'operator_name' => 'RL Express', 'total_seats' => 45],
            ['bus_number' => 'NB-7723', 'type' => 'Luxury', 'operator_name' => 'Sithumina', 'total_seats' => 50],
            ['bus_number' => 'NB-3310', 'type' => 'Semi-Luxury', 'operator_name' => 'Sithumina', 'total_seats' => 45],
        ];
        
        foreach ($buses as $busData) {
            $bus = Bus::create($busData);
            // Attach this bus to BOTH routes
            $bus->routes()->attach([$forwardRoute->id, $reverseRoute->id]);
        }
        
        $this->command->info('✅ Routes, stops, and buses created successfully!');
    }
}