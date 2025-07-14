<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PERMISOS DE MODULOS DEL SISTEMA (visibilidad en el menu)
        Permission::firstOrCreate(['name' => 'menu.catalogo', 'guard_name' => 'web', 'description' => 'Administración catálogos', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'menu.seguridad', 'guard_name' => 'web', 'description' => 'Administración de Seguridad', 'module_key' => 'seg']);

        // PERMISOS DE SEGURIDAD
        Permission::firstOrCreate(['name' => 'module.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'module.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'module.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'module.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::firstOrCreate(['name' => 'permission.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'permission.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'permission.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'permission.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::firstOrCreate(['name' => 'role.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'role.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'role.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'role.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::firstOrCreate(['name' => 'user.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'user.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'user.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::firstOrCreate(['name' => 'user.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        // CATALOGOS
        Permission::firstOrCreate(['name' => 'knowledgeArea.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeArea.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeArea.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeArea.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);

        Permission::firstOrCreate(['name' => 'knowledgeSubArea.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeSubArea.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeSubArea.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'knowledgeSubArea.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);

        Permission::firstOrCreate(['name' => 'criterion.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'criterion.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'criterion.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'criterion.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);

        Permission::firstOrCreate(['name' => 'institution.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'institution.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'institution.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'institution.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);
        
        Permission::firstOrCreate(['name' => 'call.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'call.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'call.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'call.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);

        Permission::firstOrCreate(['name' => 'event.index',  'guard_name' => 'web', 'description' => 'Leer eventos',     'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'event.store',  'guard_name' => 'web', 'description' => 'Crear eventos',    'module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'event.update', 'guard_name' => 'web', 'description' => 'Actualizar eventos','module_key' => 'cat']);
        Permission::firstOrCreate(['name' => 'event.delete', 'guard_name' => 'web', 'description' => 'Eliminar eventos', 'module_key' => 'cat']);


        // DIVULGACION
        Permission::firstOrCreate(['name' => 'article.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'article.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'article.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'article.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'article.evaluate', 'guard_name' => 'web', 'description' => 'Evaluar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'article.all', 'guard_name' => 'web', 'description' => 'Acceso a todos los Registros', 'module_key' => 'priv']);
        
        Permission::firstOrCreate(['name' => 'articleReview.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'articleReview.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'articleReview.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'articleReview.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'div']);

        Permission::firstOrCreate(['name' => 'paymentVoucher.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'paymentVoucher.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'paymentVoucher.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'paymentVoucher.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'div']);
        Permission::firstOrCreate(['name' => 'paymentVoucher.validate', 'guard_name' => 'web', 'description' => 'Validar Registros', 'module_key' => 'div']);

        
    }
}
