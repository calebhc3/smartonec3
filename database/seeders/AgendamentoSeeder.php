<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use Illuminate\Database\Seeder;

class AgendamentoSeeder extends Seeder
{
    public function run()
    {
        // Cria 100 agendamentos fictícios com dados gerados pelo Factory
        Agendamento::factory()->count(300)->create();
    }
}
