<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Criando permissões
    Permission::create(['name' => 'access_all']);
    Permission::create(['name' => 'access_aso_search']);
    Permission::create(['name' => 'access_scheduling']);
    Permission::create(['name' => 'access_leave_management']);
    Permission::create(['name' => 'access_shopee_integration']);

    }
}
