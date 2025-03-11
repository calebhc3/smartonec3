<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Buscar os perfis
        $gestao = Role::where('name', 'Gestão')->first();
        $operacional = Role::where('name', 'Operacional')->first();
        $busca = Role::where('name', 'Busca')->first();

        // Criar usuário para Gestão
        $userGestao = User::create([
            'name' => 'Gestão User',
            'email' => 'gestao@c3saude.com',
            'password' => bcrypt('password'),
        ]);
        $userGestao->assignRole($gestao);

        // Criar usuário para Operacional
        $userOperacional = User::create([
            'name' => 'Operacional User',
            'email' => 'operacional@c3saude.com',
            'password' => bcrypt('password'),
        ]);
        $userOperacional->assignRole($operacional);

        // Criar usuário para Busca
        $userBusca = User::create([
            'name' => 'Busca User',
            'email' => 'busca@c3saude.com',
            'password' => bcrypt('password'),
        ]);
        $userBusca->assignRole($busca);
    }
}