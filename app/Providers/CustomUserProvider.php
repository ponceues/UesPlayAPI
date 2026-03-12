<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Primero validar las credenciales normales (email y password)
        $plain = $credentials['password'];
        $isValidPassword = $this->hasher->check($plain, $user->getAuthPassword());
        
        if (!$isValidPassword) {
            return false;
        }
        
        // Validar que el usuario esté activo
        return $this->isUserActive($user);
    }
    
    /**
     * Verifica si el usuario está en estado activo
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    protected function isUserActive(Authenticatable $user)
    {
        // Obtener el state_id del usuario
        $stateId = $user->state_id ?? null;
        
        if (!$stateId) {
            return false;
        }
        
        // Verificar que el estado sea ACTIVE
        $state = DB::table('user_states')
            ->where('state_id', $stateId)
            ->where('code', 'ACTIVE')
            ->first();
        
        return $state !== null;
    }
}
