<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EventPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'event.index',  'description' => 'Leer eventos',     'module_key' => 'evt'],
            ['name' => 'event.store',  'description' => 'Crear eventos',    'module_key' => 'evt'],
            ['name' => 'event.update', 'description' => 'Actualizar eventos','module_key' => 'evt'],
            ['name' => 'event.delete', 'description' => 'Eliminar eventos', 'module_key' => 'evt'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm['name'],
                'guard_name' => 'web',
            ], [
                'description' => $perm['description'],
                'module_key' => $perm['module_key'],
            ]);
        }
    }
}
