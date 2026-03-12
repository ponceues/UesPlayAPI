<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckAllPermissions
{
    /**
     * Handle an incoming request.
     * Verifica si el usuario tiene TODOS los permisos especificados
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissionCodes - Lista de códigos de permisos
     */
    public function handle(Request $request, Closure $next, string ...$permissionCodes): Response
    {
        // Obtener el usuario autenticado del JWT
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'No autenticado',
                'message' => 'Debes estar autenticado para acceder a este recurso'
            ], 401);
        }
        
        // Verificar si el usuario tiene todos los permisos requeridos
        $hasAllPermissions = $this->userHasAllPermissions($user->rol_id, $permissionCodes);
        
        if (!$hasAllPermissions) {
            // Determinar qué permisos faltan
            $missingPermissions = $this->getMissingPermissions($user->rol_id, $permissionCodes);
            
            return response()->json([
                'error' => 'Acceso denegado',
                'message' => 'No tienes todos los permisos necesarios para realizar esta acción',
                'required_permissions' => $permissionCodes,
                'missing_permissions' => $missingPermissions,
                'note' => 'Se requieren todos estos permisos'
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Verifica si el rol del usuario tiene todos los permisos especificados
     *
     * @param  string  $rolId
     * @param  array  $permissionCodes
     * @return bool
     */
    protected function userHasAllPermissions(string $rolId, array $permissionCodes): bool
    {
        // Consultar cuántos de los permisos requeridos tiene el usuario
        $count = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $rolId)
            ->whereIn('permissions.code', $permissionCodes)
            ->count();
        
        // Debe tener todos los permisos solicitados
        return $count === count($permissionCodes);
    }
    
    /**
     * Obtiene la lista de permisos que le faltan al usuario
     *
     * @param  string  $rolId
     * @param  array  $permissionCodes
     * @return array
     */
    protected function getMissingPermissions(string $rolId, array $permissionCodes): array
    {
        // Obtener los permisos que sí tiene
        $hasPermissions = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $rolId)
            ->whereIn('permissions.code', $permissionCodes)
            ->pluck('permissions.code')
            ->toArray();
        
        // Retornar los que no tiene
        return array_values(array_diff($permissionCodes, $hasPermissions));
    }
}
