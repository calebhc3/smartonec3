<?php

namespace Database\Seeders;

use App\Models\Afastamento;
use Illuminate\Database\Seeder;

class AfastamentoSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 50 registros fictícios
        Afastamento::factory()->count(50)->create();
    }
}