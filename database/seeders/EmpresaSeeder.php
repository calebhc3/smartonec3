<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run()
    {
        Empresa::factory()->count(10)->create(); // Cria 5 empresas
    }
}
