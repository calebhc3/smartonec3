<?php
// app/Models/Unidade.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = ['empresa_id', 'nome', 'endereco', 'telefone'];

    // Relacionamento: Uma unidade pertence a uma empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

}
