<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'seu_numero',
        'valor_nominal',
        'data_vencimento',
        'num_dias_agenda',
        'pagador_nome',
        'pagador_tipo',
        'pagador_cpf_cnpj',
        'pagador_email',
        'pagador_endereco',
        'pagador_numero',
        'pagador_complemento',
        'pagador_bairro',
        'pagador_cidade',
        'pagador_uf',
        'pagador_cep',
        'codigo_solicitacao',
        'status',
        'pdf_path'
    ];

    protected $casts = [
        'valor_nominal' => 'decimal:2',
        'data_vencimento' => 'date',
        'num_dias_agenda' => 'integer'
    ];

    public function getPagadorEnderecoCompletoAttribute()
    {
        return sprintf(
            '%s, %s%s - %s, %s - %s, CEP: %s',
            $this->pagador_endereco,
            $this->pagador_numero,
            $this->pagador_complemento ? ' ' . $this->pagador_complemento : '',
            $this->pagador_bairro,
            $this->pagador_cidade,
            $this->pagador_uf,
            $this->pagador_cep
        );
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
