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
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'creado_por',
        'institution_id',
        'imagen',
        'archivo_pdf',
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
    
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function userRequests()
    {
        return $this->hasMany(EventUserRequest::class);
    }


}
