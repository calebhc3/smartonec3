<?php

namespace Database\Factories;

use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadeFactory extends Factory
{
    protected $model = Unidade::class;

    public function definition()
    {
        return [
            'empresa_id' => \App\Models\Empresa::factory(), // Cria uma empresa associada
            'nome' => $this->faker->city,
            'endereco' => $this->faker->address,
            'telefone' => $this->faker->phoneNumber,
        ];
    }
}