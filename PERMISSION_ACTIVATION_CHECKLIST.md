# ✅ Checklist de Activación - Sistema de Middlewares de Permisos

## Estado Actual: ✅ INSTALADO Y LISTO PARA ACTIVAR

---

## 📋 Checklist de Activación

### Fase 1: Preparación de Base de Datos ⚠️ PENDIENTE

- [ ] **1.1** Verificar que existan permisos en tabla `permissions`
  ```sql
  SELECT COUNT(*) as total FROM permissions;
  ```

- [ ] **1.2** Crear permisos básicos si no existen
  ```sql
  INSERT INTO permissions (permission_id, code, name, created_at) VALUES
  (UUID(), 'VIEW_USERS', 'Ver Usuarios', NOW()),
  (UUID(), 'CREATE_USER', 'Crear Usuario', NOW()),
  (UUID(), 'UPDATE_USER', 'Actualizar Usuario', NOW()),
  (UUID(), 'DELETE_USER', 'Eliminar Usuario', NOW()),
  (UUID(), 'MANAGE_ROLES', 'Gestionar Roles', NOW()),
  (UUID(), 'VIEW_REPORTS', 'Ver Reportes', NOW()),
  (UUID(), 'ADMIN_ACCESS', 'Acceso de Administrador', NOW());
  ```

- [ ] **1.3** Verificar que existan roles
  ```sql
  SELECT * FROM roles;
  ```

- [ ] **1.4** Asignar permisos a roles (ejemplo con rol admin)
  ```sql
  -- Obtener IDs
  SELECT rol_id FROM roles WHERE code = 'ADMIN' LIMIT 1;
  SELECT permission_id, code FROM permissions;
  
  -- Asignar todos los permisos al admin
  INSERT INTO rol_permissions (rol_id, permission_id, created_at)
  SELECT 'tu-rol-admin-id', permission_id, NOW()
  FROM permissions;
  ```

- [ ] **1.5** Verificar que usuarios tengan roles asignados
  ```sql
  SELECT user_id, name, email, rol_id FROM users;
  ```

---

### Fase 2: Activación de Middlewares en Rutas ⚠️ PENDIENTE

- [ ] **2.1** Abrir archivo `src/Application/Routes/routes.php`

- [ ] **2.2** Activar middlewares en rutas de Usuarios
  ```php
  // Buscar esta sección (líneas ~168-174):
  Route::prefix('users')->group(function () {
      Route::get('bulkUpload',[UserController::class,'generateBulkTemplate']); // ->middleware('permission:EXPORT_USERS')
      Route::post('bulkUpload',[UserController::class,'processBulkFile']); // ->middleware('permission:BULK_CREATE_USERS')
      
      Route::get('',[UserController::class,'fetchUsers']); // ->middleware('permission:VIEW_USERS')
      Route::post('',[UserController::class,'createUser']); // ->middleware('permission:CREATE_USER')
      Route::post('/{userId}',[UserController::class,'updateUser']); // ->middleware('permission:UPDATE_USER')
  });
  
  // Descomentar los middlewares:
  Route::prefix('users')->group(function () {
      Route::get('bulkUpload',[UserController::class,'generateBulkTemplate'])->middleware('permission:EXPORT_USERS');
      Route::post('bulkUpload',[UserController::class,'processBulkFile'])->middleware('permission:BULK_CREATE_USERS');
      
      Route::get('',[UserController::class,'fetchUsers'])->middleware('permission:VIEW_USERS');
      Route::post('',[UserController::class,'createUser'])->middleware('permission:CREATE_USER');
      Route::post('/{userId}',[UserController::class,'updateUser'])->middleware('permission:UPDATE_USER');
  });
  ```

- [ ] **2.3** Activar middlewares en rutas de Roles
  ```php
  // Buscar sección de roles (líneas ~71-80)
  Route::post('',[RolController::class,'createRol'])->middleware('permission:CREATE_ROLE');
  Route::put('/{rolId}',[RolController::class,'updateRol'])->middleware('permission:UPDATE_ROLE');
  Route::delete('/{rolId}',[RolController::class,'deleteRol'])->middleware('permission:DELETE_ROLE');
  ```

- [ ] **2.4** (Opcional) Activar en otras rutas según necesidad

---

### Fase 3: Pruebas ⚠️ PENDIENTE

- [ ] **3.1** Limpiar cachés
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  ```

- [ ] **3.2** Probar con usuario SIN permisos (debe dar 403)
  ```bash
  curl -X GET http://localhost/api/admin/users \
    -H "Authorization: Bearer TOKEN_SIN_PERMISOS"
  ```
  
  **Respuesta esperada:**
  ```json
  {
    "error": "Acceso denegado",
    "message": "No tienes permisos para realizar esta acción",
    "required_permission": "VIEW_USERS"
  }
  ```

- [ ] **3.3** Probar con usuario CON permisos (debe dar 200)
  ```bash
  curl -X GET http://localhost/api/admin/users \
    -H "Authorization: Bearer TOKEN_CON_PERMISOS"
  ```
  
  **Respuesta esperada:** Lista de usuarios

- [ ] **3.4** Probar sin token (debe dar 401)
  ```bash
  curl -X GET http://localhost/api/admin/users
  ```
  
  **Respuesta esperada:**
  ```json
  {
    "error": "No autenticado",
    "message": "Debes estar autenticado para acceder a este recurso"
  }
  ```

- [ ] **3.5** Ejecutar script de pruebas
  ```bash
  php artisan tinker
  require 'tests/test_permission_middleware.php';
  fullTest('tu-user-id');
  ```

---

### Fase 4: Documentación y Monitoreo ⚠️ PENDIENTE

- [ ] **4.1** Documentar permisos creados
  - Crear archivo con lista de permisos y su propósito
  - Documentar qué roles tienen qué permisos

- [ ] **4.2** Configurar logs de acceso denegado (opcional)
  - Modificar middlewares para loggear intentos fallidos
  - Revisar `storage/logs/laravel.log`

- [ ] **4.3** Crear permisos adicionales según necesidades
  - Identificar otras rutas que necesitan protección
  - Crear permisos específicos
  - Asignar a roles apropiados

---

## 🎯 Estado de Componentes

| Componente | Estado | Archivo |
|------------|--------|---------|
| CheckPermission | ✅ Creado | `app/Http/Middleware/CheckPermission.php` |
| CheckAnyPermission | ✅ Creado | `app/Http/Middleware/CheckAnyPermission.php` |
| CheckAllPermissions | ✅ Creado | `app/Http/Middleware/CheckAllPermissions.php` |
| Registro de Middlewares | ✅ Configurado | `bootstrap/app.php` |
| Rutas con Ejemplos | ✅ Listo | `src/Application/Routes/routes.php` |
| Documentación | ✅ Completa | `PERMISSION_*.md` |
| Scripts de Prueba | ✅ Creado | `tests/test_permission_middleware.php` |
| Permisos en BD | ⚠️ Verificar | Tabla `permissions` |
| Asignación a Roles | ⚠️ Verificar | Tabla `rol_permissions` |
| Activación en Rutas | ⚠️ Pendiente | Descomentar en `routes.php` |

---

## 📝 Notas Importantes

### ⚠️ Antes de Activar en Producción:

1. **Backup de Base de Datos**
   - Hacer backup completo antes de crear/modificar permisos

2. **Probar en Desarrollo Primero**
   - Activar en entorno de desarrollo
   - Probar todos los endpoints
   - Verificar que no se bloqueen flujos críticos

3. **Comunicar a Usuarios**
   - Informar sobre cambios en permisos
   - Capacitar a administradores
   - Tener plan de rollback

4. **Monitorear Primeras 24h**
   - Revisar logs de errores 403
   - Verificar que usuarios puedan acceder correctamente
   - Ajustar permisos si es necesario

---

## 🚀 Comando Rápido de Activación

Para activar el sistema completo automáticamente:

```bash
# 1. Crear permisos básicos (ejecutar SQL arriba)

# 2. Asignar permisos a roles (ejecutar SQL arriba)

# 3. Limpiar cachés
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# 4. Verificar rutas
php artisan route:list --columns=uri,method,middleware | findstr admin

# 5. Probar con Tinker
php artisan tinker
require 'tests/test_permission_middleware.php';
testPermissions();
```

---

## 📚 Recursos de Ayuda

| Necesito | Archivo | Ubicación |
|----------|---------|-----------|
| Guía rápida | `PERMISSION_QUICKSTART.md` | Raíz del proyecto |
| Documentación completa | `PERMISSION_MIDDLEWARE_DOCS.md` | Raíz del proyecto |
| Ejemplos de código | `PERMISSION_ROUTES_EXAMPLES.php` | Raíz del proyecto |
| Resumen del sistema | `PERMISSION_SYSTEM_SUMMARY.md` | Raíz del proyecto |
| Script de pruebas | `test_permission_middleware.php` | `tests/` |
| Este checklist | `PERMISSION_ACTIVATION_CHECKLIST.md` | Raíz del proyecto |

---

## ✅ Checklist Completo

Marcar con [x] cuando se complete cada paso:

```
Fase 1: Base de Datos
[ ] 1.1 Verificar permisos existentes
[ ] 1.2 Crear permisos básicos
[ ] 1.3 Verificar roles existentes
[ ] 1.4 Asignar permisos a roles
[ ] 1.5 Verificar usuarios con roles

Fase 2: Activación
[ ] 2.1 Abrir routes.php
[ ] 2.2 Activar en rutas de usuarios
[ ] 2.3 Activar en rutas de roles
[ ] 2.4 Activar en otras rutas

Fase 3: Pruebas
[ ] 3.1 Limpiar cachés
[ ] 3.2 Probar sin permisos (403)
[ ] 3.3 Probar con permisos (200)
[ ] 3.4 Probar sin token (401)
[ ] 3.5 Ejecutar script de pruebas

Fase 4: Documentación
[ ] 4.1 Documentar permisos
[ ] 4.2 Configurar logs
[ ] 4.3 Crear permisos adicionales
```

---

## 🎉 Al Completar Todo

Tu sistema de permisos estará:
- ✅ Completamente funcional
- ✅ Seguro y robusto
- ✅ Bien documentado
- ✅ Fácil de mantener
- ✅ Listo para producción

---

**Fecha de creación:** 14 de enero, 2026  
**Versión:** 1.0.0  
**Estado:** Sistema instalado, pendiente activación
