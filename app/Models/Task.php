<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'task_list_id',
        'user_id',
        'cliente_id',
        'boleto_id',
        'position',
        'priority',
        'status',
        'due_date'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }

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
