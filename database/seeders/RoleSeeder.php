<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Criação dos perfis
        $gestao = Role::create([
            'name' => 'Gestão',
            'guard_name' => 'web',
        ]);

        $operacional = Role::create([
            'name' => 'Operacional',
            'guard_name' => 'web',
        ]);

        $busca = Role::create([
            'name' => 'Busca',
            'guard_name' => 'web',
        ]);

        // Atribuir permissões ao perfil Gestão
        $gestao->givePermissionTo([
            'access_all', // Acesso a tudo
        ]);

        // Atribuir permissões ao perfil Operacional
        $operacional->givePermissionTo([
            'access_scheduling', // CRUD de agendamentos
            'access_operational_summary', // Painel de resumo operacional
        ]);

        // Atribuir permissões ao perfil Busca
        $busca->givePermissionTo([
            'access_aso_search', // Painel de busca de ASO
        ]);
    }
}