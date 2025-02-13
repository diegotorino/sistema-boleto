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
        'valor',
        'vencimento',
        'descricao',
        'status',
        'nosso_numero',
        'linha_digitavel',
        'codigo_barras',
        'url_pdf'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

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
