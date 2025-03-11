<?php

namespace Database\Seeders;

use App\Models\Unidade;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    public function run()
    {
        // Obtém todas as empresas existentes
        $empresas = Empresa::all();

        // Cria 3 unidades para cada empresa
        $empresas->each(function ($empresa) {
            Unidade::factory()->count(5)->create([
                'empresa_id' => $empresa->id,
            ]);
        });
    }
}