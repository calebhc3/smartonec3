<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AfastamentoFactory extends Factory
{
    public function definition(): array
    {
        // Gera uma data aleatória nos últimos 2 anos
        $randomDate = fn () => Carbon::now()->subDays(rand(1, 730))->format('Y-m-d');

        return [
            'empresa' => $this->faker->company(), // Adicione esta linha
            'unidade' => $this->faker->company(), // Adicione esta linha
            // Seção 1: Dados Iniciais
            'nome' => $this->faker->name(),
            'cpf' => $this->faker->unique()->numerify('###########'), // CPF sem máscara
            'data_admissao' => $randomDate(),
            'cargo' => $this->faker->randomElement(['Analista', 'Gerente', 'Assistente', 'Técnico']),
            'setor' => $this->faker->randomElement(['RH', 'TI', 'Financeiro', 'Operações']),
            'genero' => $this->faker->randomElement(['Masculino', 'Feminino', 'Outro']),
            'data_psc' => $randomDate(),
            'data_notificacao' => $randomDate(),
            'andamento_processo_shopee' => $this->faker->randomElement(['Pendente', 'Em análise', 'Concluído']),
            'codigo' => $this->faker->unique()->bothify('AF###??'),
            'data_nascimento' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'idade' => $this->faker->numberBetween(20, 60),

            // Seção 2: Controle Interno C3 Saúde
            'data_carta_dut_enviada_assinatura' => $this->faker->optional()->date(),
            'data_carta_dut_recebida_assinada' => $this->faker->optional()->date(),
            'data_carta_dut_enviada_colaborador' => $this->faker->optional()->date(),
            'data_ultimo_dia_trabalhado' => $randomDate(),
            'condicao_abertura_cat' => $this->faker->boolean(),
            'cid' => $this->faker->randomElement(['F32.9', 'M54.5', 'J18.9']),
            'patologia' => $this->faker->randomElement(['Depressão', 'Hérnia de Disco', 'Pneumonia']),
            'descricao_patologia' => $this->faker->sentence(),
            'especie_beneficio_inss' => $this->faker->randomElement(['Auxílio-doença', 'Aposentadoria por invalidez']),
            'afastada_atividades' => $this->faker->boolean(),
            'afastados_inss' => $this->faker->boolean(),
            'limbo_previdenciario' => $this->faker->boolean(),

            // Seção 3: Dados Iniciais da Perícia
            'alta_antecipada' => $this->faker->boolean(),
            'entrada_pericia' => $randomDate(),
            'data_pericia' => $randomDate(),
            'tipo_pericia' => $this->faker->randomElement(['Inicial', 'Renovação', 'Revisão']),
            'pericia_realizada' => $this->faker->boolean(),
            'numero_beneficio' => $this->faker->numerify('#########'),
            'status_pericia' => $this->faker->randomElement(['Aprovado', 'Negado', 'Pendente']),
            'motivo' => $this->faker->sentence(),
            'nexo_tecnico' => $this->faker->boolean(),
            'contestacao' => $this->faker->boolean(),

            // Seção 4: Notificação Shopee Retorno Colaborador
            'termino_previsto_beneficio' => $randomDate(),
            'notificar_shopee_retorno' => $randomDate(),
            'data_prevista_exame_retorno' => $randomDate(),
            'clinica' => $this->faker->company(),
            'afastamento_inicial' => $randomDate(),
            'data_recebimento_aso' => $this->faker->optional()->date(),
            'data_envio_aso_shopee' => $this->faker->optional()->date(),

            // Seção 5: Informação para Folha de Pagamento
            'status_atual' => $this->faker->randomElement(['Afastado', 'Retornado', 'Em análise']),
            'data_retorno_atividades' => $this->faker->optional()->date(),
            'periodo_restricao' => $this->faker->randomElement(['30 dias', '60 dias', 'Sem restrição']),
        ];
    }
}