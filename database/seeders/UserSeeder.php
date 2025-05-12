<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Criar roles principais
        $roles = [
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Gestão', 'guard_name' => 'web'],
            ['name' => 'Busca', 'guard_name' => 'web'],
            ['name' => 'Afastamentos', 'guard_name' => 'web']
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Usuário master
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

        // Usuários de Agendamento (Gestão)
        $gestores = [
            [
                'name' => 'Marcondes',
                'email' => 'agendamento@c3saude.com.br',
                'password' => 'Agendamento123!'
            ],
            [
                'name' => 'João',
                'email' => 'agendamento1@c3saude.com.br',
                'password' => 'Agendamento123!'
            ],
            [
                'name' => 'Jennifer',
                'email' => 'agendamento3@c3saude.com.br',
                'password' => 'Agendamento123!'
            ],
        ];

        foreach ($gestores as $gestor) {
            $user = User::firstOrCreate(
                ['email' => $gestor['email']],
                [
                    'name' => $gestor['name'],
                    'password' => Hash::make($gestor['password']),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
            $user->assignRole('Gestão');
        }

        // Usuários de Busca
        $buscaUsers = [
            ['name' => 'Regiane',   'email' => 'busca0@smartonec3.com'],
            ['name' => 'Thassiane', 'email' => 'busca01@smartonec3.com'],
            ['name' => 'Gabriel',   'email' => 'busca02@smartonec3.com'],
        ];

        foreach ($buscaUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Busca123!'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
            $user->assignRole('Busca');
        }

        // Usuários de Afastamentos
        $afastamentoUsers = [
            ['name' => 'Cecilia', 'email' => 'afastamentos01@smartonec3.com'],
            ['name' => 'Gabriel', 'email' => 'afastamentos02@smartonec3.com'],
        ];

        foreach ($afastamentoUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Afastamentos123!'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]
            );
            $user->assignRole('Afastamentos');
        }

        // Atualizar descrições se o campo existir
        if (Schema::hasColumn('roles', 'description')) {
            Role::where('name', 'Admin')->update(['description' => 'Acesso completo ao sistema']);
            Role::where('name', 'Gestão')->update(['description' => 'Acesso à gestão de agendamentos e relatórios']);
            Role::where('name', 'Busca')->update(['description' => 'Acesso ao painel de busca de ASOs']);
            Role::where('name', 'Afastamentos')->update(['description' => 'Acesso à gestão de afastamentos']);
        }

        $this->command->info('Usuários e roles criados com sucesso!');
    }
}
