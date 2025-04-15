<?php

// database/factories/AfastamentoFactory.php

namespace Database\Factories;

use App\Models\Afastamento;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AfastamentoFactory extends Factory
{
    protected $model = Afastamento::class;

    public function definition(): array
    {
        $generos = ['Masculino', 'Feminino', 'Outro'];
        $tipoPericia = ['presencial', 'documental', 'a_iniciar', 'nao_realizada'];
        $statusPericia = ['deferido', 'indeferido', 'em_analise', 'pericia_cancelada', 'em_agendamento'];
        $statusAtual = ['recorrente', 'afastado', 'liberado_ao_retorno', 'desligado', 'liberado_com_termo', 'liberado_com_restricao', 'licenca_maternidade', 'pericia_cancelada', 'rescisao_indireta', 'falecimento'];
        $beneficios = ['b31_auxilio_doenca_previdenciario', 'b91_auxilio_doenca_acidentario', 'b32_aposentadoria_por_invalidez'];
        $motivos = ['constatao_de_incapacidade_laborativa', 'nao_constatacao_da_Incapacidade_laborativa'];
        $afastamentoInicial = ['Afastado', 'Falta_histórico'];

        $dataNascimento = $this->faker->dateTimeBetween('-60 years', '-18 years');
        $idade = Carbon::parse($dataNascimento)->age;

        return [
            'nome' => $this->faker->name(),
            'cpf' =>  $this->faker->regexify('\d{3}\.\d{3}\.\d{3}-\d{2}'),
            'data_nascimento' => $dataNascimento->format('Y-m-d'),
            'idade' => $idade,
            'empresa' => $this->faker->company(),
            'cnpj_unidade' => $this->faker->regexify('\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}'),
            'nome_unidade' => $this->faker->company(),
            'data_admissao' => $this->faker->date(),
            'cargo' => $this->faker->jobTitle(),
            'setor' => $this->faker->word(),
            'genero' => $this->faker->randomElement($generos),
            'data_psc' => $this->faker->date(),
            'data_notificacao' => now(),
            'codigo' => Str::random(10),

            'data_carta_dut_enviada_assinatura' => $this->faker->optional()->date(),
            'data_carta_dut_recebida_assinada' => $this->faker->optional()->date(),
            'data_carta_dut_enviada_colaborador' => $this->faker->optional()->date(),
            'data_ultimo_dia_trabalhado' => $this->faker->optional()->date(),
            'condicao_abertura_cat' => $this->faker->boolean(),
            'cid' => strtoupper($this->faker->bothify('S##')),
            'patologia' => $this->faker->word(),
            'especie_beneficio_inss' => $this->faker->randomElement($beneficios),
            'afastada_atividades' => $this->faker->boolean(),
            'afastados_inss' => $this->faker->boolean(),
            'limbo_previdenciario' => $this->faker->boolean(),

            'alta_antecipada' => $this->faker->boolean(),
            'entrada_pericia' => $this->faker->optional()->date(),
            'data_pericia' => $this->faker->optional()->date(),
            'tipo_pericia' => $this->faker->randomElement($tipoPericia),
            'pericia_realizada' => $this->faker->boolean(),
            'numero_beneficio' => $this->faker->numerify('###########'),
            'status_pericia' => $this->faker->randomElement($statusPericia),
            'motivo' => $this->faker->randomElement($motivos),
            'nexo_tecnico' => $this->faker->boolean(),
            'contestacao' => $this->faker->boolean(),

            'termino_previsto_beneficio' => $this->faker->optional()->date(),
            'notificar_shopee_retorno' => $this->faker->optional()->date(),
            'data_prevista_exame_retorno' => $this->faker->optional()->date(),
            'clinica' => $this->faker->company(),
            'afastamento_inicial' => $this->faker->randomElement($afastamentoInicial),
            'data_recebimento_aso' => $this->faker->optional()->date(),
            'data_envio_aso_shopee' => $this->faker->optional()->date(),

            'status_atual' => $this->faker->randomElement($statusAtual),
            'data_retorno_atividades' => $this->faker->optional()->date(),
            'periodo_restricao' => $this->faker->optional()->sentence(),
            'comentario' => $this->faker->optional()->paragraph(),
        ];
    }
}
