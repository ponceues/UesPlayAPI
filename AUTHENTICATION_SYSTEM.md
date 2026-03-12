# Sistema de Autenticación Personalizada

## Descripción
Se ha implementado un sistema de autenticación personalizada que **solo permite el acceso a usuarios con estado ACTIVE**. La validación se realiza automáticamente durante el proceso de login con `Auth::attempt()`.

## Archivos Modificados/Creados

### 1. `app/Providers/CustomUserProvider.php` (NUEVO)
Este es el proveedor de autenticación personalizado que extiende `EloquentUserProvider` de Laravel.

**Funcionalidades:**
- Valida las credenciales del usuario (email y password)
- Verifica automáticamente que el usuario tenga estado ACTIVE
- Rechaza el login si el usuario no está activo, incluso si la contraseña es correcta

**Método clave:**
```php
public function validateCredentials(Authenticatable $user, array $credentials)
{
    // Valida password
    $isValidPassword = $this->hasher->check($plain, $user->getAuthPassword());
    
    // Valida estado ACTIVE
    return $isValidPassword && $this->isUserActive($user);
}
```

### 2. `app/Providers/AppServiceProvider.php` (MODIFICADO)
Se registró el Custom User Provider en el método `boot()`:

```php
Auth::provider('custom_eloquent', function ($app, array $config) {
    return new CustomUserProvider($app['hash'], $config['model']);
});
```

### 3. `config/auth.php` (MODIFICADO)
Se cambió el driver del provider de usuarios:

```php
'providers' => [
    'users' => [
        'driver' => 'custom_eloquent',  // Antes era 'eloquent'
        'model' => env('AUTH_MODEL', App\Models\User::class),
    ],
],
```

### 4. `app/Models/User.php` (MODIFICADO)
Se agregaron los campos `state_id` y `rol_id` a los campos fillable:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'state_id',
    'rol_id',
];
```

### 5. `src/Domain/Services/AuthService.php` (MODIFICADO)
Se mejoró el método `validateUsersAttempt()` para proporcionar mensajes de error más específicos:

- Si el usuario no existe: "Credenciales incorrectas"
- Si el usuario existe pero la contraseña es incorrecta: "Contraseña incorrecta"
- Si el usuario existe pero no está activo: "Tu cuenta no está activa"

## Cómo Funciona

### Flujo de Autenticación

1. **Usuario intenta hacer login** con email y password
2. **Auth::attempt($credentials)** es llamado
3. **CustomUserProvider** toma el control:
   - Busca el usuario por email
   - Verifica que la contraseña sea correcta
   - **Verifica que el estado sea ACTIVE** consultando la tabla `user_states`
   - Si todo es correcto, genera el token JWT
   - Si algo falla (password incorrecto o estado no activo), retorna `false`
4. **AuthService** maneja la respuesta:
   - Si el token es generado exitosamente, lo retorna
   - Si falla, determina el motivo y lanza una excepción con mensaje específico

### Ventajas de esta Implementación

1. **Centralizada**: La validación del estado se hace automáticamente en el provider
2. **Segura**: No hay forma de bypassear la validación del estado
3. **Reutilizable**: Todos los métodos que usen `Auth::attempt()` heredan esta validación
4. **Mantenible**: La lógica de validación está en un solo lugar
5. **Mensajes claros**: El usuario recibe feedback específico sobre por qué falló el login

## Pruebas Recomendadas

1. **Usuario activo con credenciales correctas**: ✅ Debe generar token
2. **Usuario activo con contraseña incorrecta**: ❌ "Contraseña incorrecta"
3. **Usuario inactivo con credenciales correctas**: ❌ "Tu cuenta no está activa"
4. **Usuario que no existe**: ❌ "Credenciales incorrectas"

## Notas Importantes

- El campo `state_id` en la tabla `users` debe estar relacionado con la tabla `user_states`
- La tabla `user_states` debe tener un campo `code` con el valor 'ACTIVE' para usuarios activos
- Este sistema funciona con JWT (php-open-source-saver/jwt-auth)
