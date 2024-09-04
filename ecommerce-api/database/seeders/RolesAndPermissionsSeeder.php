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
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage tags']);
        Permission::create(['name' => 'admin privelages']);



        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['admin privelages', 'manage users', 'manage products', 'manage orders', 'manage categories', 'manage tags']);

        $vendor = Role::create(['name' => 'vendor']);
        $vendor->givePermissionTo(['manage products']);

        $customer = Role::create(['name' => 'customer']);
        $customer->givePermissionTo(['manage orders']);
    }
}
