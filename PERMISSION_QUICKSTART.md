# Sistema de Middlewares de Permisos - Guía Rápida

## 🚀 Instalación Completa ✅

El sistema de middlewares de permisos ha sido instalado y configurado correctamente.

---

## 📋 Middlewares Disponibles

### 1. `permission:CODIGO` - Permiso único
```php
Route::post('/users', [UserController::class, 'create'])
    ->middleware('permission:CREATE_USER');
```

### 2. `permission.any:CODIGO1,CODIGO2` - Cualquiera (OR)
```php
Route::put('/users/{id}', [UserController::class, 'update'])
    ->middleware('permission.any:UPDATE_USER,UPDATE_OWN_USER');
```

### 3. `permission.all:CODIGO1,CODIGO2` - Todos (AND)
```php
Route::delete('/users/{id}', [UserController::class, 'delete'])
    ->middleware('permission.all:DELETE_USER,ADMIN_ACCESS');
```

---

## 🎯 Uso Rápido

### Proteger una ruta individual:
```php
Route::post('/admin/roles', [RolController::class, 'createRol'])
    ->middleware('permission:CREATE_ROLE');
```

### Proteger un grupo de rutas:
```php
Route::prefix('admin/users')
    ->middleware(['jwt.auth', 'permission:MANAGE_USERS'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'create']);
        Route::put('/{id}', [UserController::class, 'update']);
    });
```

---

## 📁 Archivos del Sistema

```
✅ app/Http/Middleware/CheckPermission.php
✅ app/Http/Middleware/CheckAnyPermission.php
✅ app/Http/Middleware/CheckAllPermissions.php
✅ bootstrap/app.php (middlewares registrados)
✅ src/Application/Routes/routes.php (ejemplos comentados)
```

---

## 🔧 Cómo Funciona

1. Usuario hace login → Recibe JWT
2. Usuario hace request con JWT en header
3. Middleware extrae `rol_id` del usuario
4. Middleware consulta permisos del rol en DB
5. Compara con permiso requerido → Permite o Deniega

---

## 📊 Estructura de BD

### Tablas involucradas:
- `users` - contiene `rol_id`
- `roles` - define roles del sistema
- `permissions` - define permisos disponibles (campo `code`)
- `rol_permissions` - asigna permisos a roles

---

## ✅ Activar en tus Rutas

### Paso 1: Descomentar los middlewares
Abre `src/Application/Routes/routes.php` y descomentar las líneas:

```php
// ANTES (comentado):
Route::post('',[UserController::class,'createUser']); // ->middleware('permission:CREATE_USER')

// DESPUÉS (activo):
Route::post('',[UserController::class,'createUser'])->middleware('permission:CREATE_USER');
```

### Paso 2: Verificar permisos en BD
Asegúrate de que el código del permiso existe en la tabla `permissions`:

```sql
SELECT * FROM permissions WHERE code = 'CREATE_USER';
```

### Paso 3: Asignar permisos al rol
```sql
INSERT INTO rol_permissions (rol_id, permission_id, created_at)
VALUES ('rol-id', 'permission-id', NOW());
```

---

## 🧪 Testing

### Probar con curl:
```bash
# Con permisos correctos (200)
curl -X POST http://localhost/api/admin/users \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test"}'

# Sin permisos (403)
curl -X POST http://localhost/api/admin/users \
  -H "Authorization: Bearer TOKEN_SIN_PERMISOS"

# Sin token (401)
curl -X POST http://localhost/api/admin/users
```

---

## 🎨 Respuestas de Error

### 401 - No autenticado
```json
{
    "error": "No autenticado",
    "message": "Debes estar autenticado para acceder a este recurso"
}
```

### 403 - Sin permisos
```json
{
    "error": "Acceso denegado",
    "message": "No tienes permisos para realizar esta acción",
    "required_permission": "CREATE_USER"
}
```

---

## 📚 Documentación Completa

- **`PERMISSION_MIDDLEWARE_DOCS.md`** - Documentación completa y detallada
- **`PERMISSION_ROUTES_EXAMPLES.php`** - 10 ejemplos prácticos de uso
- Este archivo - Guía rápida de referencia

---

## 🔥 Ejemplos Comunes

### CRUD de Usuarios:
```php
Route::prefix('admin/users')->middleware('jwt.auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:VIEW_USERS');
    
    Route::post('/', [UserController::class, 'create'])
        ->middleware('permission:CREATE_USER');
    
    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission:UPDATE_USER');
    
    Route::delete('/{id}', [UserController::class, 'delete'])
        ->middleware('permission:DELETE_USER');
});
```

### Operaciones Críticas:
```php
Route::delete('/system/purge', [SystemController::class, 'purge'])
    ->middleware('permission.all:ADMIN_ACCESS,CONFIRM_DELETE,PURGE_SYSTEM');
```

### Flexibilidad con OR:
```php
Route::get('/reports', [ReportController::class, 'index'])
    ->middleware('permission.any:VIEW_OWN_REPORTS,VIEW_ALL_REPORTS');
```

---

## ⚡ Comandos Útiles

```bash
# Limpiar caché después de cambios
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Ver todas las rutas registradas
php artisan route:list

# Ver rutas con middlewares
php artisan route:list --columns=uri,method,middleware
```

---

## 🐛 Troubleshooting

### Error: "Middleware not found"
**Solución:** Ejecutar `php artisan config:clear`

### Error: "No autenticado" en rutas protegidas
**Solución:** Verificar que se envía `Authorization: Bearer {token}` en header

### Error: "Acceso denegado" con usuario admin
**Solución:** Verificar que el rol tenga el permiso asignado en `rol_permissions`

---

## 🎯 Próximos Pasos

1. ✅ Crear permisos en la tabla `permissions`
2. ✅ Asignar permisos a roles en `rol_permissions`
3. ✅ Descomentar middlewares en `routes.php`
4. ✅ Probar con diferentes usuarios
5. ✅ Ajustar permisos según necesidades

---

## 💡 Tips

- Usa nombres descriptivos para permisos: `CREATE_USER`, `DELETE_RESOURCE`
- Combina `permission.all` para acciones críticas
- Usa `permission.any` para dar flexibilidad
- Documenta qué permisos requiere cada ruta
- Revisa logs si algo no funciona: `storage/logs/laravel.log`

---

**¡Sistema listo para usar! 🎉**
