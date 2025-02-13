<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boleto;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'cpf_cnpj',
        'telefone',
        'endereco'
    ];

    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }
}
