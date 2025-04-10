<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->company,
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'telefone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->companyEmail,
            'endereco' => $this->faker->address,
        ];
    }
}