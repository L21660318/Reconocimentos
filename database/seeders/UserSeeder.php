<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Primero crear los roles
        $admin = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrador']
        );
        
        $postulant = Role::updateOrCreate(
            ['name' => 'Postulante'],
            ['description' => 'Postulante']
        );
        
        $reviewer = Role::updateOrCreate(
            ['name' => 'Revisor'],
            ['description' => 'Revisor']
        );
        
        $editor = Role::updateOrCreate(
            ['name' => 'Editor'],
            ['description' => 'Editor']
        );
        
        $financial = Role::updateOrCreate(
            ['name' => 'Finanzas'],
            ['description' => 'Finanzas']
        );

        // Usuarios con manejo de duplicados
        $users = [
            [
                'name' => 'Administrador',
                'email' => 'vitervolopezcaballero@gmail.com',
                'password' => Hash::make('Password'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => 'Admin'
            ],
            [
                'name' => 'Diego Administrador',
                'email' => 'diego@gmail.com',
                'password' => Hash::make('Password'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => 'Postulante'
            ],
            [
                'name' => 'Fernanda Administradora',
                'email' => 'fernanda@gmail.com',
                'password' => Hash::make('Password'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => 'Revisor'
            ],
            [
                'name' => 'Vitervo Postulante',
                'email' => 'vitervo.lc@cenidet.tecnm.mx',
                'password' => Hash::make('Password'),
                'institution_id' => 1,
                'email_verified_at' => now(),
                'role' => 'Editor'
            ],
            [
                'name' => 'Vitervo Revisor',
                'email' => 'veranocientifico@cenidet.tecnm.mx',
                'password' => Hash::make('Password'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => 'Editor'
            ],
            [
                'name' => 'Vitervo Editor',
                'email' => 'vitervo@cenidet.edu.mx',
                'password' => Hash::make('Password'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => 'Finanzas'
            ],
            [
                'name' => 'Miguel Editor',
                'email' => 'chano@gmail.com',
                'password' => Hash::make('12345678'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => null
            ],
            [
                'name' => 'Financiero',
                'email' => 'financiero@gmail.com',
                'password' => Hash::make('12345678'),
                'institution_id' => null,
                'email_verified_at' => now(),
                'role' => null
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'] ?? null;
            unset($userData['role']);
            
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            if ($roleName) {
                $user->syncRoles($roleName);
            }
        }
    }
}