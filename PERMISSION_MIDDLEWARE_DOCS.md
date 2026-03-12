# Middleware de Permisos - Documentación

## Descripción General
Sistema de middlewares para validar permisos de usuarios basándose en JWT y roles. Los permisos se verifican contra la base de datos en tiempo real, consultando las tablas `rol_permissions` y `permissions`.

## Middlewares Disponibles

### 1. `permission` - Permiso Único
Verifica que el usuario tenga UN permiso específico.

**Uso:**
```php
Route::post('/users', [UserController::class, 'create'])
    ->middleware('permission:CREATE_USER');
```

**Respuesta en caso de fallo:**
```json
{
    "error": "Acceso denegado",
    "message": "No tienes permisos para realizar esta acción",
    "required_permission": "CREATE_USER"
}
```

---

### 2. `permission.any` - Cualquier Permiso (OR Lógico)
Verifica que el usuario tenga AL MENOS UNO de los permisos especificados.

**Uso:**
```php
Route::put('/users/{id}', [UserController::class, 'update'])
    ->middleware('permission.any:UPDATE_USER,UPDATE_OWN_USER');
```

**Casos de uso:**
- Usuario puede actualizar todos los usuarios O solo el suyo
- Usuario puede ver reportes O exportar datos
- Usuario puede crear recursos O editarlos

**Respuesta en caso de fallo:**
```json
{
    "error": "Acceso denegado",
    "message": "No tienes permisos para realizar esta acción",
    "required_permissions": ["UPDATE_USER", "UPDATE_OWN_USER"],
    "note": "Se requiere al menos uno de estos permisos"
}
```

---

### 3. `permission.all` - Todos los Permisos (AND Lógico)
Verifica que el usuario tenga TODOS los permisos especificados.

**Uso:**
```php
Route::delete('/system/purge', [SystemController::class, 'purge'])
    ->middleware('permission.all:MANAGE_SYSTEM,DELETE_DATA,ADMIN_ACCESS');
```

**Casos de uso:**
- Acciones críticas que requieren múltiples permisos
- Operaciones de administración avanzada
- Funciones de auditoría que requieren varios niveles de acceso

**Respuesta en caso de fallo:**
```json
{
    "error": "Acceso denegado",
    "message": "No tienes todos los permisos necesarios para realizar esta acción",
    "required_permissions": ["MANAGE_SYSTEM", "DELETE_DATA", "ADMIN_ACCESS"],
    "missing_permissions": ["DELETE_DATA"],
    "note": "Se requieren todos estos permisos"
}
```

---

## Ejemplos de Uso

### Ejemplo 1: Proteger una ruta individual
```php
Route::post('/admin/users', [UserController::class, 'createUser'])
    ->middleware('permission:CREATE_USER');
```

### Ejemplo 2: Proteger un grupo de rutas
```php
Route::prefix('admin/roles')->middleware('permission:MANAGE_ROLES')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'create']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'delete']);
});
```

### Ejemplo 3: Combinar múltiples middlewares
```php
Route::post('/resources/approve', [ResourceController::class, 'approve'])
    ->middleware(['jwt.auth', 'permission.all:APPROVE_RESOURCES,MANAGE_RESOURCES']);
```

### Ejemplo 4: Diferentes permisos para diferentes métodos
```php
Route::prefix('resources')->group(function () {
    // Solo requiere lectura
    Route::get('/', [ResourceController::class, 'index'])
        ->middleware('permission:VIEW_RESOURCES');
    
    // Requiere creación
    Route::post('/', [ResourceController::class, 'create'])
        ->middleware('permission:CREATE_RESOURCE');
    
    // Requiere actualización O ser el propietario
    Route::put('/{id}', [ResourceController::class, 'update'])
        ->middleware('permission.any:UPDATE_RESOURCE,UPDATE_OWN_RESOURCE');
    
    // Requiere eliminación Y confirmación administrativa
    Route::delete('/{id}', [ResourceController::class, 'delete'])
        ->middleware('permission.all:DELETE_RESOURCE,ADMIN_CONFIRM');
});
```

### Ejemplo 5: Rutas públicas vs protegidas
```php
Route::prefix('blog')->group(function () {
    // Rutas públicas - sin middleware
    Route::get('/posts', [BlogController::class, 'index']);
    Route::get('/posts/{id}', [BlogController::class, 'show']);
    
    // Rutas protegidas - con middleware
    Route::middleware('permission:CREATE_POST')->group(function () {
        Route::post('/posts', [BlogController::class, 'create']);
    });
    
    Route::middleware('permission.any:UPDATE_POST,UPDATE_OWN_POST')->group(function () {
        Route::put('/posts/{id}', [BlogController::class, 'update']);
    });
    
    Route::middleware('permission.all:DELETE_POST,ADMIN_ACCESS')->group(function () {
        Route::delete('/posts/{id}', [BlogController::class, 'delete']);
    });
});
```

---

## Estructura de Base de Datos

### Tabla: `permissions`
```sql
- permission_id (string, PK)
- code (string) -- Ej: 'CREATE_USER', 'DELETE_RESOURCE'
- name (string) -- Ej: 'Crear Usuario', 'Eliminar Recurso'
- menu_id (string, FK)
- created_at (timestamp)
```

### Tabla: `rol_permissions`
```sql
- rol_id (string, FK)
- permission_id (string, FK)
- created_at (timestamp)
```

### Tabla: `users`
```sql
- user_id (string, PK)
- name (string)
- email (string)
- password (string)
- rol_id (string, FK)
- state_id (string, FK)
```

---

## Flujo de Autenticación y Autorización

1. **Usuario hace login** → Recibe JWT con información del usuario
2. **Usuario hace request** → Envía JWT en header `Authorization: Bearer {token}`
3. **Middleware JWT valida el token** → Extrae información del usuario
4. **Middleware de permisos verifica**:
   - Obtiene `rol_id` del usuario autenticado
   - Consulta tabla `rol_permissions` para obtener permisos del rol
   - Compara con el permiso requerido por la ruta
   - Permite o deniega el acceso

---

## Códigos de Respuesta HTTP

| Código | Significado | Cuándo ocurre |
|--------|-------------|---------------|
| 200 | OK | Permiso válido, acción ejecutada |
| 401 | No autenticado | No se envió JWT o es inválido |
| 403 | Acceso denegado | Usuario autenticado pero sin permisos |

---

## Mejores Prácticas

### ✅ Hacer:
- Usar códigos de permisos descriptivos (`CREATE_USER`, `VIEW_REPORTS`)
- Combinar `jwt.auth` con middlewares de permisos
- Usar `permission.any` para flexibilidad
- Usar `permission.all` para acciones críticas
- Documentar los permisos requeridos en cada ruta

### ❌ Evitar:
- No verificar permisos en acciones críticas
- Usar códigos de permisos ambiguos (`ACTION_1`, `PERM_X`)
- Duplicar lógica de permisos en controladores
- Hardcodear roles en lugar de usar permisos

---

## Testing

### Probar con curl:
```bash
# Sin token (401)
curl -X POST http://localhost/api/admin/users

# Con token pero sin permisos (403)
curl -X POST http://localhost/api/admin/users \
  -H "Authorization: Bearer {token_sin_permisos}"

# Con token y permisos correctos (200)
curl -X POST http://localhost/api/admin/users \
  -H "Authorization: Bearer {token_con_permisos}" \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com"}'
```

### Probar con Postman:
1. Crear request con método correspondiente
2. Agregar header: `Authorization: Bearer {your_jwt_token}`
3. Verificar respuesta según permisos del usuario

---

## Archivos del Sistema

```
app/Http/Middleware/
├── CheckPermission.php          # Middleware para permiso único
├── CheckAnyPermission.php       # Middleware para OR lógico
└── CheckAllPermissions.php      # Middleware para AND lógico

bootstrap/
└── app.php                      # Registro de middlewares

src/Application/Routes/
└── routes.php                   # Definición de rutas con middlewares
```

---

## Troubleshooting

### Error: "No autenticado"
**Problema:** JWT no está presente o es inválido
**Solución:** Verificar que el token se envía en el header correcto

### Error: "Acceso denegado"
**Problema:** Usuario no tiene el permiso requerido
**Solución:** 
1. Verificar que el rol del usuario tenga el permiso asignado
2. Verificar que el código del permiso coincida exactamente
3. Revisar tabla `rol_permissions` en base de datos

### Error: No se aplica el middleware
**Problema:** Middleware no registrado correctamente
**Solución:** 
1. Verificar registro en `bootstrap/app.php`
2. Limpiar caché: `php artisan cache:clear && php artisan config:clear`

---

## Extensiones Futuras

### Ideas para mejorar:
1. **Caché de permisos**: Cachear permisos del usuario para reducir consultas
2. **Permisos por recurso**: Verificar permisos específicos en recursos individuales
3. **Permisos dinámicos**: Permisos que cambian según contexto (horario, ubicación, etc.)
4. **Auditoría**: Registrar intentos de acceso denegados
5. **Permisos heredados**: Sistema jerárquico de permisos

---

## Contacto y Soporte

Para preguntas o problemas con el sistema de permisos:
- Revisar logs en `storage/logs/laravel.log`
- Verificar configuración de base de datos
- Consultar documentación de JWT: php-open-source-saver/jwt-auth
