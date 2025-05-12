<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Criando os cargos/roles
        $gestao = Role::create([
            'name' => 'Gestão',
            'guard_name' => 'web',
        ]);

        $busca = Role::create([
            'name' => 'Busca',
            'guard_name' => 'web',
        ]);

        $agendamento = Role::create([
            'name' => 'Agendamento',
            'guard_name' => 'web',
        ]);

        $afastamento = Role::create([
            'name' => 'Afastamento',
            'guard_name' => 'web',
        ]);

        $shopee = Role::create([
            'name' => 'Shopee',
            'guard_name' => 'web',
        ]);

        // Permissões atribuídas a cada role
        $gestao->givePermissionTo([
            'access_all', // Admin master
        ]);

        $busca->givePermissionTo([
            'access_aso_search', // Painel de busca de ASOs
        ]);

        $agendamento->givePermissionTo([
            'access_scheduling', // CRUD de agendamentos
        ]);

        $afastamento->givePermissionTo([
            'access_leave_management', // Gestão de afastamentos
        ]);

        $shopee->givePermissionTo([
            'access_shopee_integration', // Permissões específicas da Shopee
        ]);
    }
}
