<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage orders']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['manage users', 'manage products', 'manage orders']);

        $vendor = Role::create(['name' => 'vendor']);
        $vendor->givePermissionTo(['manage products', 'manage orders']);

        $customer = Role::create(['name' => 'customer']);
    }
}
