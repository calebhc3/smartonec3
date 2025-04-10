<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAgendamentos extends Model
{
    use HasFactory;

    // Nome da tabela associada ao model
    protected $table = 'registro_agendamentos';

    // Colunas que podem ser preenchidas via mass assignment
    protected $fillable = [
        'data_registro',
        'total_agendamentos',
    ];

    // Caso queira manipular o formato de data (opcional)
    protected $dates = [
        'data_registro',
    ];
}
