<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class AclSync extends Command
{
    protected $signature = 'acl:update';

    protected $description = 'Update acl list from delivered configuration';

    public function handle(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $acl = config('system.acl');

        if ($acl === null) {
            $this->error('Missing system.php configuration file, or "acl" key in this file');
            return;
        }

        try {
            foreach ($acl as $role => $permissions) {
                collect($permissions)->each(function (string $name) {
                    Permission::firstOrCreate(['name' => $name]);
                    $this->info('syncing permission: ' . $name);
                });
                $role = Role::firstOrCreate(['name' => $role]);
                $this->info('syncing role: ' . $role->name);
                $role->syncPermissions($permissions);
            }
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }
}
