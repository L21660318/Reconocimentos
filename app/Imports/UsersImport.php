<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function model(array $row)
    {
        // Verifica si el usuario ya existe
        $user = User::where('email', $row['email'])->first();

        if (!$user) {
            // Crea nuevo usuario
            $user = new User([
                'name' => $row['nombre'] ?? $row['name'] ?? 'Nuevo Usuario',
                'email' => $row['email'],
                'password' => Hash::make($row['password'] ?? 'password'), // Considera enviar contraseñas temporales
                'knowledge_area_id' => $row['area_conocimiento'] ?? null,
                'institution_id' => $row['institucion'] ?? null,
            ]);
            
            $user->save();
        }

        // Asocia el usuario al evento si no está ya asociado
        if (!$user->events()->where('event_id', $this->eventId)->exists()) {
            $user->events()->attach($this->eventId);
        }

        return $user;
    }
}