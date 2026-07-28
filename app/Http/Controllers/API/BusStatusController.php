<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Route;
use App\Models\BusStatusUpdate;
use Illuminate\Http\Request;
use App\Models\Stop;

class BusStatusController extends Controller
{
    // GET /api/buses - Get all buses
    public function getBuses()
    {
        $buses = Bus::with('routes')->get();
        return response()->json([
            'success' => true,
            'data' => $buses
        ]);
    }
    
    public function updateLocation(Request $request)
    {
        // ✅ Log the request to see if it's being called
        \Log::info('updateLocation called', [
            'bus_id' => $request->bus_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        // Validate the request
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'accuracy' => 'nullable|integer',
        ]);

        // Find the latest status update for this bus
        $latestUpdate = BusStatusUpdate::where('bus_id', $request->bus_id)
            ->latest()
            ->first();

        if (!$latestUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'No status update found for this bus'
            ], 404);
        }

        // ✅ UPDATE GPS COORDINATES
        $latestUpdate->update([
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'gps_accuracy' => $request->accuracy,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'latitude' => $request->lat,
                'longitude' => $request->lng,
                'accuracy' => $request->accuracy,
            ]
        ]);
    }

    // POST /api/routes - Create a new route
    public function storeRoute(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:routes',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'direction' => 'required|in:forward,reverse',
            'stops' => 'required|array|min:2',
            'is_active' => 'boolean',
            'note' => 'nullable|string',
        ]);

        $route = Route::create([
            'name' => $request->name,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'direction' => $request->direction,
            'is_active' => $request->is_active ?? true,
            'note' => $request->note,
        ]);

        // Create stops
        foreach ($request->stops as $index => $stopName) {
            Stop::create([
                'route_id' => $route->id,
                'name' => $stopName,
                'sequence' => $index + 1,
                'type' => $index === 0 ? 'origin' : ($index === count($request->stops) - 1 ? 'dest' : 'mid'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Route created successfully',
            'data' => $route->load('stops')
        ], 201);
    }

    // PUT /api/routes/{id} - Update a route
    public function updateRoute(Request $request, $id)
    {
        $route = Route::find($id);
        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Route not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:routes,name,' . $id,
            'origin' => 'required|string',
            'destination' => 'required|string',
            'direction' => 'required|in:forward,reverse',
            'stops' => 'required|array|min:2',
            'is_active' => 'boolean',
            'note' => 'nullable|string',
        ]);

        $route->update([
            'name' => $request->name,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'direction' => $request->direction,
            'is_active' => $request->is_active ?? true,
            'note' => $request->note,
        ]);

        // Delete old stops and create new ones
        $route->stops()->delete();
        foreach ($request->stops as $index => $stopName) {
            Stop::create([
                'route_id' => $route->id,
                'name' => $stopName,
                'sequence' => $index + 1,
                'type' => $index === 0 ? 'origin' : ($index === count($request->stops) - 1 ? 'dest' : 'mid'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Route updated successfully',
            'data' => $route->load('stops')
        ]);
    }

    // DELETE /api/routes/{id} - Delete a route
    public function destroyRoute($id)
    {
        $route = Route::find($id);
        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Route not found'], 404);
        }

        $route->stops()->delete();
        $route->delete();

        return response()->json([
            'success' => true,
            'message' => 'Route deleted successfully'
        ]);
    }

    // POST /api/buses - Create a new bus
    public function store(Request $request)
    {
        $request->validate([
            'bus_number' => 'required|unique:buses',
            'type' => 'required|in:Luxury,Semi-Luxury,Normal',
            'operator_name' => 'required|string',
            'total_seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $bus = Bus::create($request->all());

        // Attach to default route (Panadura-Maharagama)
        $route = Route::first();
        if ($route) {
            $bus->routes()->attach($route->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bus created successfully',
            'data' => $bus
        ], 201);
    }

    // PUT /api/buses/{id} - Update a bus
    public function update(Request $request, $id)
    {
        $bus = Bus::find($id);
        if (!$bus) {
            return response()->json(['success' => false, 'message' => 'Bus not found'], 404);
        }

        $request->validate([
            'bus_number' => 'required|unique:buses,bus_number,' . $id,
            'type' => 'required|in:Luxury,Semi-Luxury,Normal',
            'operator_name' => 'required|string',
            'total_seats' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $bus->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Bus updated successfully',
            'data' => $bus
        ]);
    }

    // DELETE /api/buses/{id} - Delete a bus
    public function destroy($id)
    {
        $bus = Bus::find($id);
        if (!$bus) {
            return response()->json(['success' => false, 'message' => 'Bus not found'], 404);
        }

        $bus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bus deleted successfully'
        ]);
    }



    // GET /api/routes - Get all routes
    public function getRoutes()
    {
        $routes = Route::with('stops')->get();
        return response()->json([
            'success' => true,
            'data' => $routes
        ]);
    }
    
    // GET /api/routes/{id}/stops - Get stops for a specific route
    public function getRouteStops($id)
    {
        $route = Route::with('stops')->find($id);
        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Route not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $route->stops
        ]);
    }
    
    public function updateStatus(Request $request)
    {
        // ✅ Log the request
        \Log::info('updateStatus called', $request->all());

        // Validate the request
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'status' => 'required|string',
            'direction' => 'required|in:forward,reverse',
            'current_stop' => 'required|string',
            'seats_available' => 'required|integer|min:0',
            'eta_minutes' => 'nullable|integer',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'gps_accuracy' => 'nullable|integer',
        ]);

        // Get the route
        $route = Route::where('direction', $request->direction)->first();
        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
        }

        // ✅ SAVE TO DATABASE
        $statusUpdate = BusStatusUpdate::create([
            'bus_id' => $request->bus_id,
            'route_id' => $route->id,
            'user_id' => $request->user()->id,
            'status' => $request->status,
            'current_stop' => $request->current_stop,
            'seats_available' => $request->seats_available,
            'eta_minutes' => $request->eta_minutes,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'gps_accuracy' => $request->gps_accuracy,
            'direction' => $request->direction,
            'update_time' => now(),
        ]);

        // Also update the bus's route assignment if needed
        // (You can update the bus_route table here if the bus changes route)

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $statusUpdate
        ]);
    }
    
    // GET /api/status/latest - Get latest bus statuses (for passenger polling)
    public function getLatestStatus(Request $request)
    {
        $direction = $request->get('direction', 'forward');
        $route = Route::where('direction', $direction)->first();
        
        if (!$route) {
            return response()->json(['success' => false, 'data' => []]);
        }
        
        // Get buses with their latest status for this route
        $buses = Bus::whereHas('routes', function($q) use ($route) {
            $q->where('routes.id', $route->id);
        })->get();
        
        $result = [];
        foreach ($buses as $bus) {
            // Get the latest status update for this bus on this route
            $latestStatus = BusStatusUpdate::where('bus_id', $bus->id)
                ->where('route_id', $route->id)
                ->latest('update_time')
                ->first();
            
            // If no status update exists, create a default one
            if (!$latestStatus) {
                // Create a default "At Depot" status
                $latestStatus = BusStatusUpdate::create([
                    'bus_id' => $bus->id,
                    'route_id' => $route->id,
                    'user_id' => 1, // admin user
                    'status' => 'At Depot',
                    'current_stop' => $route->origin,
                    'seats_available' => $bus->total_seats,
                    'eta_minutes' => null,
                    'direction' => $direction,
                    'update_time' => now(),
                ]);
            }
            
            $result[] = [
                'bus_id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'type' => $bus->type,
                'operator_name' => $bus->operator_name,
                'bus_status' => $bus->status,
                'current_stop' => $latestStatus->current_stop,
                'status' => $latestStatus->status,
                'eta_minutes' => $latestStatus->eta_minutes,
                'seats_available' => $latestStatus->seats_available,
                'direction' => $latestStatus->direction,
                // ✅ ADD THESE LINES FOR GPS
                'latitude' => $latestStatus->latitude,      // ← GPS lat
                'longitude' => $latestStatus->longitude,    // ← GPS lng
                'gps_accuracy' => $latestStatus->gps_accuracy,
                'driver_name' => $bus->driver_name,
                'driver_phone' => $bus->driver_phone,
            ];
        }
        
        return response()->json([
            'success' => true,
            'route' => $route->name,
            'direction' => $direction,
            'stops' => $route->stops,
            'buses' => $result,
            'last_updated' => now()->toIso8601String()
        ]);
    }
}