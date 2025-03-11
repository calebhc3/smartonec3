<?php
// app/Models/Empresa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'cnpj', 'telefone', 'email', 'endereco'];

    // Relacionamento: Uma empresa tem muitas unidades
    public function unidades()
    {
        return $this->hasMany(Unidade::class);
    }


}
