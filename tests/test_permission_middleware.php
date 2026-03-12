<?php

/**
 * Script de Prueba para Middlewares de Permisos
 * 
 * Ejecutar desde Tinker: php artisan tinker
 * require 'tests/test_permission_middleware.php';
 */

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PermissionMiddlewareTest {
    
    /**
     * Verifica la configuración básica del sistema
     */
    public static function checkSetup() {
        echo "\n=== VERIFICACIÓN DE CONFIGURACIÓN ===\n\n";
        
        // 1. Verificar que existan las tablas
        $tables = ['users', 'permissions', 'rol_permissions'];
        foreach ($tables as $table) {
            $exists = DB::getSchemaBuilder()->hasTable($table);
            echo ($exists ? "✅" : "❌") . " Tabla '{$table}': " . ($exists ? "Existe" : "NO existe") . "\n";
        }
        
        echo "\n";
        
        // 2. Verificar que existan permisos
        $permissionCount = DB::table('permissions')->count();
        echo ($permissionCount > 0 ? "✅" : "⚠️") . " Permisos en BD: {$permissionCount}\n";
        
        // 3. Verificar que existan roles con permisos asignados
        $rolPermissionCount = DB::table('rol_permissions')->count();
        echo ($rolPermissionCount > 0 ? "✅" : "⚠️") . " Permisos asignados a roles: {$rolPermissionCount}\n";
        
        // 4. Verificar que existan usuarios
        $userCount = DB::table('users')->count();
        echo ($userCount > 0 ? "✅" : "⚠️") . " Usuarios en BD: {$userCount}\n";
        
        echo "\n";
    }
    
    /**
     * Lista todos los permisos disponibles
     */
    public static function listPermissions() {
        echo "\n=== PERMISOS DISPONIBLES ===\n\n";
        
        $permissions = DB::table('permissions')
            ->select('permission_id', 'code', 'name')
            ->get();
        
        if ($permissions->isEmpty()) {
            echo "⚠️ No hay permisos registrados en el sistema\n";
            return;
        }
        
        foreach ($permissions as $permission) {
            echo "• {$permission->code}\n";
            echo "  Nombre: {$permission->name}\n";
            echo "  ID: {$permission->permission_id}\n\n";
        }
    }
    
    /**
     * Lista los permisos de un rol específico
     */
    public static function listRolePermissions($rolId) {
        echo "\n=== PERMISOS DEL ROL: {$rolId} ===\n\n";
        
        $permissions = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $rolId)
            ->select('permissions.code', 'permissions.name')
            ->get();
        
        if ($permissions->isEmpty()) {
            echo "⚠️ Este rol no tiene permisos asignados\n";
            return;
        }
        
        foreach ($permissions as $permission) {
            echo "✅ {$permission->code} - {$permission->name}\n";
        }
        
        echo "\nTotal: " . $permissions->count() . " permisos\n";
    }
    
    /**
     * Verifica si un usuario tiene un permiso específico
     */
    public static function checkUserPermission($userId, $permissionCode) {
        echo "\n=== VERIFICAR PERMISO ===\n\n";
        
        $user = DB::table('users')->where('user_id', $userId)->first();
        
        if (!$user) {
            echo "❌ Usuario no encontrado\n";
            return false;
        }
        
        echo "Usuario: {$user->name} ({$user->email})\n";
        echo "Rol ID: {$user->rol_id}\n";
        echo "Permiso a verificar: {$permissionCode}\n\n";
        
        $hasPermission = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $user->rol_id)
            ->where('permissions.code', $permissionCode)
            ->exists();
        
        if ($hasPermission) {
            echo "✅ El usuario TIENE el permiso '{$permissionCode}'\n";
        } else {
            echo "❌ El usuario NO TIENE el permiso '{$permissionCode}'\n";
        }
        
        return $hasPermission;
    }
    
    /**
     * Simula una verificación de middleware
     */
    public static function simulateMiddlewareCheck($userId, $permissionCode) {
        echo "\n=== SIMULACIÓN DE MIDDLEWARE ===\n\n";
        
        $user = DB::table('users')->where('user_id', $userId)->first();
        
        if (!$user) {
            echo "❌ 401 - No autenticado\n";
            echo "Mensaje: Debes estar autenticado para acceder a este recurso\n";
            return false;
        }
        
        echo "Usuario autenticado: {$user->name}\n";
        echo "Verificando permiso: {$permissionCode}\n\n";
        
        $hasPermission = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $user->rol_id)
            ->where('permissions.code', $permissionCode)
            ->exists();
        
        if ($hasPermission) {
            echo "✅ 200 - Acceso permitido\n";
            echo "El usuario puede ejecutar esta acción\n";
            return true;
        } else {
            echo "❌ 403 - Acceso denegado\n";
            echo "Mensaje: No tienes permisos para realizar esta acción\n";
            echo "Permiso requerido: {$permissionCode}\n";
            return false;
        }
    }
    
    /**
     * Prueba completa del sistema
     */
    public static function runFullTest($userId) {
        echo "\n╔═══════════════════════════════════════════════╗\n";
        echo "║   PRUEBA COMPLETA DE MIDDLEWARES DE PERMISOS  ║\n";
        echo "╚═══════════════════════════════════════════════╝\n";
        
        // 1. Verificar configuración
        self::checkSetup();
        
        // 2. Obtener información del usuario
        $user = DB::table('users')->where('user_id', $userId)->first();
        
        if (!$user) {
            echo "❌ Usuario con ID '{$userId}' no encontrado\n";
            return;
        }
        
        echo "=== INFORMACIÓN DEL USUARIO ===\n\n";
        echo "Nombre: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Rol ID: {$user->rol_id}\n\n";
        
        // 3. Listar permisos del rol
        self::listRolePermissions($user->rol_id);
        
        // 4. Probar algunos permisos comunes
        echo "\n=== PRUEBAS DE PERMISOS COMUNES ===\n";
        
        $commonPermissions = [
            'VIEW_USERS',
            'CREATE_USER',
            'UPDATE_USER',
            'DELETE_USER',
            'MANAGE_ROLES',
            'VIEW_REPORTS'
        ];
        
        foreach ($commonPermissions as $permission) {
            $hasPermission = DB::table('rol_permissions')
                ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
                ->where('rol_permissions.rol_id', $user->rol_id)
                ->where('permissions.code', $permission)
                ->exists();
            
            echo ($hasPermission ? "✅" : "❌") . " {$permission}\n";
        }
        
        echo "\n╔═══════════════════════════════════════════════╗\n";
        echo "║              PRUEBA COMPLETADA                ║\n";
        echo "╚═══════════════════════════════════════════════╝\n\n";
    }
}

// Funciones helper para usar directamente
function testPermissions() {
    PermissionMiddlewareTest::checkSetup();
}

function listPermissions() {
    PermissionMiddlewareTest::listPermissions();
}

function listRolePermissions($rolId) {
    PermissionMiddlewareTest::listRolePermissions($rolId);
}

function checkUserPermission($userId, $permissionCode) {
    return PermissionMiddlewareTest::checkUserPermission($userId, $permissionCode);
}

function simulateCheck($userId, $permissionCode) {
    return PermissionMiddlewareTest::simulateMiddlewareCheck($userId, $permissionCode);
}

function fullTest($userId) {
    PermissionMiddlewareTest::runFullTest($userId);
}

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║  Script de Prueba para Middlewares de Permisos Cargado   ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\nFunciones disponibles:\n";
echo "  • testPermissions()                     - Verifica configuración\n";
echo "  • listPermissions()                     - Lista todos los permisos\n";
echo "  • listRolePermissions('rol-id')         - Lista permisos de un rol\n";
echo "  • checkUserPermission('user-id', 'CODE') - Verifica permiso de usuario\n";
echo "  • simulateCheck('user-id', 'CODE')      - Simula middleware\n";
echo "  • fullTest('user-id')                   - Prueba completa\n";
echo "\nEjemplo: fullTest('tu-user-id')\n\n";
