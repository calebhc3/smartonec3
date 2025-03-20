<?php

namespace Database\Factories;

use App\Models\Agendamento;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AgendamentoFactory extends Factory
{
    protected $model = Agendamento::class;

    public function definition()
    {
        // Gera uma data aleatória para o exame (entre 1 e 30 dias no futuro ou passado)
        $dataExame = $this->faker->randomElement([
            Carbon::now()->addDays(rand(1, 30)), // Futuro
            Carbon::now()->subDays(rand(1, 30)), // Passado
        ]);

        // Define o status com base na data do exame
        $status = $dataExame < Carbon::now()
            ? $this->faker->randomElement(['pendente', 'cancelado', 'não compareceu']) // Atrasado
            : $this->faker->randomElement(['pendente', 'realizado', 'ASO ok', 'ASO enviado']); // No prazo ou concluído

        return [
            'empresa_id' => Empresa::inRandomOrder()->first()->id,
            'cidade_atendimento' => $this->faker->city, // Gera uma cidade aleatória
            'estado_atendimento' => $this->faker->stateAbbr, // Gera uma sigla de estado aleatória
            'data_exame' => $dataExame,
            'horario_exame' => $dataExame->copy()->addHours(rand(1, 24))->format('H:i:s'),
            'nome_funcionario' => $this->faker->name, // Gera um nome aleatório
            'contato_whatsapp' => $this->faker->phoneNumber, // Gera um número de telefone aleatório
            'doc_identificacao_rg' => $this->faker->unique()->numerify('########'), // Gera um RG aleatório
            'doc_identificacao_cpf' => $this->faker->unique()->numerify('###########'), // Gera um CPF aleatório
            'data_nascimento' => $this->faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'), // Gera uma data de nascimento aleatória
            'data_admissao' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'), // Gera uma data de admissão aleatória
            'funcao' => $this->faker->jobTitle, // Gera uma função aleatória
            'setor' => $this->faker->word, // Gera um setor aleatório
            'tipo_exame' => $this->faker->randomElement(['admissional', 'periodico', 'demissional', 'retorno_trabalho', 'mudanca_funcao', 'avaliacao_clinica']),
            'status' => $status, // Status definido com base na data do exame
            'sla' => $this->faker->randomElement(['clinico', 'clinico_complementar', 'clinico_acidos']),
            'data_solicitacao' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'), // Gera uma data de solicitação aleatória
            'nome_solicitante' => $this->faker->name, // Gera um nome de solicitante aleatório
            'email_solicitante' => $this->faker->unique()->email, // Gera um e-mail aleatório
            'data_devolutiva' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d H:i:s'), // Gera uma data de devolutiva aleatória
            'comparecimento' => $this->faker->randomElement(['nao_informado', 'compareceu', 'nao_compareceu']),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}