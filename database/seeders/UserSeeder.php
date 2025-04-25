<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Criar roles principais (sem description até a migração ser executada)
        $roles = [
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Gestão', 'guard_name' => 'web'],
            ['name' => 'Busca', 'guard_name' => 'web'],
            ['name' => 'Afastamentos', 'guard_name' => 'web']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        // Criar usuário Master (Admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@c3saude.com.br'],
            [
                'name' => 'Administrador Master',
                'password' => Hash::make('SenhaSegura123!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        $admin->assignRole('Admin');

        // Criar usuário de Gestão
        $gestao = User::firstOrCreate(
            ['email' => 'gestao@c3saude.com.br'],
            [
                'name' => 'Usuário Gestão',
                'password' => Hash::make('Gestao123!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        $gestao->assignRole('Gestão');

        // Criar usuário de Busca
        $busca = User::firstOrCreate(
            ['email' => 'busca@c3saude.com.br'],
            [
                'name' => 'Usuário Busca',
                'password' => Hash::make('Busca123!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        $busca->assignRole('Busca');

        // Criar usuário de Afastamentos
        $afastamentos = User::firstOrCreate(
            ['email' => 'afastamentos@c3saude.com.br'],
            [
                'name' => 'Usuário Afastamentos',
                'password' => Hash::make('Afastamentos123!'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        $afastamentos->assignRole('Afastamentos');

        // Atualizar descrições após a migração (opcional)
        if (Schema::hasColumn('roles', 'description')) {
            Role::where('name', 'Admin')->update(['description' => 'Acesso completo ao sistema']);
            Role::where('name', 'Gestão')->update(['description' => 'Acesso à gestão de equipes e relatórios']);
            Role::where('name', 'Busca')->update(['description' => 'Acesso aos módulos de busca']);
            Role::where('name', 'Afastamentos')->update(['description' => 'Acesso à gestão de afastamentos']);
        }

        $this->command->info('Usuários e roles criados com sucesso!');
    }
}