<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckAnyPermission
{
    /**
     * Handle an incoming request.
     * Verifica si el usuario tiene AL MENOS UNO de los permisos especificados
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissionCodes - Lista de códigos de permisos separados por comas
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
        
        // Verificar si el usuario tiene al menos uno de los permisos requeridos
        $hasAnyPermission = $this->userHasAnyPermission($user->rol_id, $permissionCodes);
        
        if (!$hasAnyPermission) {
            return response()->json([
                'error' => 'Acceso denegado',
                'message' => 'No tienes permisos para realizar esta acción',
                'required_permissions' => $permissionCodes,
                'note' => 'Se requiere al menos uno de estos permisos'
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Verifica si el rol del usuario tiene al menos uno de los permisos especificados
     *
     * @param  string  $rolId
     * @param  array  $permissionCodes
     * @return bool
     */
    protected function userHasAnyPermission(string $rolId, array $permissionCodes): bool
    {
        // Consultar en la base de datos si el rol tiene alguno de los permisos
        $count = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $rolId)
            ->whereIn('permissions.code', $permissionCodes)
            ->count();
        
        return $count > 0;
    }
}
