<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permissionCode
     */
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        // Obtener el usuario autenticado del JWT
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'No autenticado',
                'message' => 'Debes estar autenticado para acceder a este recurso'
            ], 401);
        }
        
        // Verificar si el usuario tiene el permiso requerido
        $hasPermission = $this->userHasPermission($user->rol_id, $permissionCode);
        
        if (!$hasPermission) {
            return response()->json([
                'error' => 'Acceso denegado',
                'message' => 'No tienes permisos para realizar esta acción',
                'required_permission' => $permissionCode
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Verifica si el rol del usuario tiene el permiso especificado
     *
     * @param  string  $rolId
     * @param  string  $permissionCode
     * @return bool
     */
    protected function userHasPermission(string $rolId, string $permissionCode): bool
    {
        // Consultar en la base de datos si el rol tiene el permiso
        $permission = DB::table('rol_permissions')
            ->join('permissions', 'rol_permissions.permission_id', '=', 'permissions.permission_id')
            ->where('rol_permissions.rol_id', $rolId)
            ->where('permissions.code', $permissionCode)
            ->first();
        
        return $permission !== null;
    }
}
