<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bus;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'status', 'bus_id', 'created_at')->get();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function approve($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->status = 'approved';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,conductor'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'user' => $user
        ]);
    }

    // ✅ NEW: Update user details (name, email, bus_id, status)
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,conductor',
            'bus_id' => 'nullable|exists:buses,id',
            'status' => 'required|in:pending,approved',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'bus_id' => $request->bus_id,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // ✅ NEW: Delete a user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    // ✅ NEW: Get all buses for dropdown
    public function getBuses()
    {
        $buses = Bus::select('id', 'bus_number')->get();
        return response()->json([
            'success' => true,
            'data' => $buses
        ]);
    }
}