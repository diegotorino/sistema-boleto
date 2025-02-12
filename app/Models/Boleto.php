<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'cpf_cnpj',
        'email',
        'valor',
        'vencimento',
        'status',
        'nosso_numero',
        'linha_digitavel',
        'url_pdf',
        'mensagem',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento' => 'date',
    ];

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pendente' => 'yellow',
            'pago' => 'green',
            'vencido' => 'red',
            'cancelado' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado',
            default => 'Desconhecido',
        };
    }
}
