<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Criando permissões
        Permission::create(['name' => 'access_all']); // Gestão
        Permission::create(['name' => 'access_scheduling']); // Operacional - Agendamentos
        Permission::create(['name' => 'access_operational_summary']); // Operacional - Resumo Operacional
        Permission::create(['name' => 'access_aso_search']); // Busca - Busca de ASO

        // Criando perfis (roles)
        $gestao = Role::create(['name' => 'gestao']);
        $operacional = Role::create(['name' => 'operacional']);
        $busca = Role::create(['name' => 'busca']);

        // Atribuindo permissões aos perfis
        $gestao->givePermissionTo([
            'access_all',
        ]);

        $operacional->givePermissionTo([
            'access_scheduling',
            'access_operational_summary',
        ]);

        $busca->givePermissionTo([
            'access_aso_search',
        ]);
    }
}
