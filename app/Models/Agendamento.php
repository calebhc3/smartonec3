<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail; // Importação do Mail
use App\Mail\LembreteExameMail; // Corrigido: Importação do Mailable correto
use Spatie\Activitylog\Traits\LogsActivity;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract; // Importe a interface

class Agendamento extends Model implements AuditableContract // Implementa a interface AuditableContract
{
    use HasFactory;

    use Auditable;

            // Propriedade temporária para contar "não compareceu"
        protected $naoCompareceuCount = 0;

        // Método para definir a contagem
        public function setNaoCompareceuCount($count)
        {
            $this->naoCompareceuCount = $count;
        }
    
        // Método para obter a contagem
        public function getNaoCompareceuCount()
        {
            return $this->naoCompareceuCount;
        }

    // Definir quais alterações serão auditadas
    protected $auditInclude = [
        'status',
        'data_solicitacao',
        'data_devolutiva',
        'nome_funcionario',
        // Adicione outros campos que são relevantes para o log de atividade
    ];

    protected $fillable = [
        'empresa_id',
        'cnpj_unidade',
        'nome_unidade',
        'cidade_atendimento',
        'estado_atendimento',
        'data_exame',
        'horario_exame',
        'nome_funcionario',
        'contato_whatsapp',
        'doc_identificacao_rg',
        'doc_identificacao_cpf',
        'data_nascimento',
        'data_admissao',
        'funcao',
        'setor',
        'tipo_exame',
        'status', 
        'nao_compareceu_count',
        'sla',
        'user_id',
        'data_solicitacao',
        'nome_solicitante',
        'email_solicitante',
        'whatsapp_solicitante',
        'data_devolutiva',
        'clinica_agendada',
        'comparecimento',

    ];

    protected $casts = [
        'data_solicitacao' => 'datetime',
        'data_devolutiva' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function enviarLembreteExame()
    {
        // Verifica se a empresa vinculada ao agendamento tem um e-mail
        $empresaEmail = $this->empresa->email;

        if ($empresaEmail) {
            // Envia o e-mail de lembrete para o e-mail da empresa vinculada
            Mail::to($empresaEmail)
                ->send(new LembreteExameMail($this)); // Envia o lembrete de exame
        } else {
            // Caso a empresa não tenha e-mail, ou trate o erro de alguma forma
            \Log::warning("Agendamento {$this->id} não tem email de empresa associado.");
        }
    }


    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

}
