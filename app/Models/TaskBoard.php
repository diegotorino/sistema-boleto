<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBoard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'cliente_id',
        'boleto_id',
        'due_date',
        'priority',
        'position'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function boleto()
    {
        return $this->belongsTo(Boleto::class);
    }
}
