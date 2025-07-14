<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'creado_por',
    ];

    // Opcional: relación con usuario
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
    
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id')
            ->withTimestamps();
    }

    public function revisiones()
    {
        return $this->belongsToMany(User::class, 'event_reviewer', 'event_id', 'reviewer_id')
            ->withPivot(['tipo', 'estatus', 'comentario'])
            ->withTimestamps();
    }


}
