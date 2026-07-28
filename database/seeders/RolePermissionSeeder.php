<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Bus;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────────────
        // 1. CREATE PERMISSIONS
        // ──────────────────────────────────────────────

        $permissions = [
            // Bus permissions
            'view buses',
            'create buses',
            'edit buses',
            'delete buses',
            
            // Route permissions
            'view routes',
            'create routes',
            'edit routes',
            'delete routes',
            
            // Status permissions
            'view status',
            'update status',
            'share gps',
            
            // User permissions
            'view users',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ──────────────────────────────────────────────
        // 2. CREATE ROLES
        // ──────────────────────────────────────────────

        // Admin Role - Has ALL permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Conductor Role - Limited permissions
        $conductorRole = Role::create(['name' => 'conductor']);
        $conductorRole->givePermissionTo([
            'view buses',
            'view routes',
            'view status',
            'update status',
            'share gps',
        ]);

        // ──────────────────────────────────────────────
        // 3. CREATE USERS
        // ──────────────────────────────────────────────

        // 👑 Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@haltinfo.lk',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        // 🚌 Conductor Users (One per bus)

        // Find buses
        $bus1 = Bus::where('bus_number', 'NB-2341')->first();
        $bus2 = Bus::where('bus_number', 'NB-5589')->first();
        $bus3 = Bus::where('bus_number', 'NB-7723')->first();
        $bus4 = Bus::where('bus_number', 'NB-3310')->first();

        // Conductor for NB-2341
        if ($bus1) {
            $conductor1 = User::create([
                'name' => 'Conductor - NB-2341',
                'email' => 'conductor1@haltinfo.lk',
                'password' => bcrypt('password'),
                'bus_id' => $bus1->id,
            ]);
            $conductor1->assignRole('conductor');
        }

        // Conductor for NB-5589
        if ($bus2) {
            $conductor2 = User::create([
                'name' => 'Conductor - NB-5589',
                'email' => 'conductor2@haltinfo.lk',
                'password' => bcrypt('password'),
                'bus_id' => $bus2->id,
            ]);
            $conductor2->assignRole('conductor');
        }

        // Conductor for NB-7723
        if ($bus3) {
            $conductor3 = User::create([
                'name' => 'Conductor - NB-7723',
                'email' => 'conductor3@haltinfo.lk',
                'password' => bcrypt('password'),
                'bus_id' => $bus3->id,
            ]);
            $conductor3->assignRole('conductor');
        }

        // Conductor for NB-3310
        if ($bus4) {
            $conductor4 = User::create([
                'name' => 'Conductor - NB-3310',
                'email' => 'conductor4@haltinfo.lk',
                'password' => bcrypt('password'),
                'bus_id' => $bus4->id,
            ]);
            $conductor4->assignRole('conductor');
        }

        $this->command->info('✅ Roles and permissions created successfully!');
        $this->command->info('Admin: admin@haltinfo.lk / password');
        $this->command->info('Conductor1: conductor1@haltinfo.lk / password (NB-2341)');
        $this->command->info('Conductor2: conductor2@haltinfo.lk / password (NB-5589)');
        $this->command->info('Conductor3: conductor3@haltinfo.lk / password (NB-7723)');
        $this->command->info('Conductor4: conductor4@haltinfo.lk / password (NB-3310)');
    }
}