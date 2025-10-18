<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // City permissions
            'city.create',
            'city.read',
            'city.show',
            'city.update',
            'city.delete',
            'city.list',

            // Estate permissions
            'estate.create',
            'estate.read',
            'estate.show',
            'estate.update',
            'estate.delete',
            'estate.list',

            // Material permissions
            'material.create',
            'material.read',
            'material.show',
            'material.update',
            'material.delete',
            'material.list',

            // Plan permissions
            'plan.create',
            'plan.read',
            'plan.show',
            'plan.update',
            'plan.delete',
            'plan.list',

            // Task permissions
            'task.create',
            'task.read',
            'task.show',
            'task.update',
            'task.delete',
            'task.list',

            // User permissions
            'user.create',
            'user.read',
            'user.show',
            'user.update',
            'user.delete',
            'user.list',

            'car.list',
            'car.show',
            'car.create',
            'car.update',
            'car.delete',

            'training.user.remove',
            'training.user.add',
            'training.user.assign',
            'training.user.assign_all',
            'training.user.assign_by_role',
            'training.user.assign_selected',
            'training.user.list',
            'training.category.create',
            'training.category.update',
            'training.category.delete',
            'training.category.list',
            'training.create',
            'training.read',
            'training.show',
            'training.update',
            'training.delete',
            'training.list',

            // Training file permissions
            'training.file.attach',
            'training.file.list',
            'training.file.delete',

            // Company permissions
            'company.view',
            'company.create',
            'company.update',
            'company.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $technicianRole = Role::firstOrCreate([
            'name' => 'technician',
            'guard_name' => 'web',
        ]);

        $driverRole = Role::firstOrCreate([
            'name' => 'driver',
            'guard_name' => 'web',
        ]);

        $objectManagerRole = Role::firstOrCreate([
            'name' => 'object_manager',
            'guard_name' => 'web',
        ]);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo(Permission::all());
    }
}
