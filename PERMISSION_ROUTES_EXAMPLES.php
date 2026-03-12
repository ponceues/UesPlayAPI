<?php

/**
 * EJEMPLO DE RUTAS CON MIDDLEWARES DE PERMISOS
 * 
 * Este archivo muestra ejemplos completos de cómo aplicar los middlewares
 * de permisos en diferentes escenarios.
 * 
 * NO INCLUIR ESTE ARCHIVO EN PRODUCCIÓN - Solo para referencia
 */

use Illuminate\Support\Facades\Route;

// ===================================================================
// EJEMPLO 1: CRUD Básico con Permisos Granulares
// ===================================================================
Route::prefix('admin/users')->middleware('jwt.auth')->group(function () {
    // Ver lista de usuarios - requiere VIEW_USERS
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:VIEW_USERS');
    
    // Ver un usuario específico - requiere VIEW_USERS
    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('permission:VIEW_USERS');
    
    // Crear usuario - requiere CREATE_USER
    Route::post('/', [UserController::class, 'create'])
        ->middleware('permission:CREATE_USER');
    
    // Actualizar usuario - requiere UPDATE_USER O UPDATE_OWN_USER
    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission.any:UPDATE_USER,UPDATE_OWN_USER');
    
    // Eliminar usuario - requiere DELETE_USER Y ADMIN_ACCESS
    Route::delete('/{id}', [UserController::class, 'delete'])
        ->middleware('permission.all:DELETE_USER,ADMIN_ACCESS');
});

// ===================================================================
// EJEMPLO 2: Grupo de Rutas con Permiso Base
// ===================================================================
Route::prefix('admin/resources')
    ->middleware(['jwt.auth', 'permission:MANAGE_RESOURCES'])
    ->group(function () {
        // Todas estas rutas requieren MANAGE_RESOURCES
        Route::get('/', [ResourceController::class, 'index']);
        Route::post('/', [ResourceController::class, 'create']);
        Route::put('/{id}', [ResourceController::class, 'update']);
        
        // Esta ruta requiere MANAGE_RESOURCES + DELETE_RESOURCE
        Route::delete('/{id}', [ResourceController::class, 'delete'])
            ->middleware('permission:DELETE_RESOURCE');
    });

// ===================================================================
// EJEMPLO 3: Diferentes Niveles de Acceso
// ===================================================================
Route::prefix('reports')->middleware('jwt.auth')->group(function () {
    // Usuario básico puede ver sus propios reportes
    Route::get('/my-reports', [ReportController::class, 'myReports'])
        ->middleware('permission:VIEW_OWN_REPORTS');
    
    // Supervisor puede ver reportes de su área
    Route::get('/area-reports', [ReportController::class, 'areaReports'])
        ->middleware('permission.any:VIEW_AREA_REPORTS,VIEW_ALL_REPORTS');
    
    // Admin puede ver todos los reportes
    Route::get('/all-reports', [ReportController::class, 'allReports'])
        ->middleware('permission:VIEW_ALL_REPORTS');
    
    // Exportar requiere permisos especiales
    Route::post('/export', [ReportController::class, 'export'])
        ->middleware('permission.all:EXPORT_REPORTS,VIEW_ALL_REPORTS');
});

// ===================================================================
// EJEMPLO 4: Sistema de Aprobación Multi-nivel
// ===================================================================
Route::prefix('resources/approval')->middleware('jwt.auth')->group(function () {
    // Cualquier usuario con permisos puede enviar a revisión
    Route::post('/{id}/submit', [ApprovalController::class, 'submit'])
        ->middleware('permission:SUBMIT_FOR_APPROVAL');
    
    // Revisor o supervisor puede aprobar nivel 1
    Route::post('/{id}/level1', [ApprovalController::class, 'approveLevel1'])
        ->middleware('permission.any:APPROVE_LEVEL1,APPROVE_ALL');
    
    // Solo supervisores pueden aprobar nivel 2
    Route::post('/{id}/level2', [ApprovalController::class, 'approveLevel2'])
        ->middleware('permission.any:APPROVE_LEVEL2,APPROVE_ALL');
    
    // Aprobación final requiere múltiples permisos
    Route::post('/{id}/final', [ApprovalController::class, 'approveFinal'])
        ->middleware('permission.all:APPROVE_FINAL,SUPERVISOR_ACCESS,ADMIN_VERIFY');
    
    // Rechazar requiere justificación y permisos
    Route::post('/{id}/reject', [ApprovalController::class, 'reject'])
        ->middleware('permission.any:REJECT_SUBMISSION,APPROVE_ALL');
});

// ===================================================================
// EJEMPLO 5: Gestión de Configuración del Sistema
// ===================================================================
Route::prefix('system/settings')->middleware('jwt.auth')->group(function () {
    // Ver configuración - cualquier admin
    Route::get('/', [SettingsController::class, 'index'])
        ->middleware('permission:VIEW_SETTINGS');
    
    // Actualizar configuración general - requiere permisos de configuración
    Route::put('/general', [SettingsController::class, 'updateGeneral'])
        ->middleware('permission.all:UPDATE_SETTINGS,MANAGE_SYSTEM');
    
    // Actualizar configuración de seguridad - requiere permisos elevados
    Route::put('/security', [SettingsController::class, 'updateSecurity'])
        ->middleware('permission.all:UPDATE_SECURITY,ADMIN_ACCESS,SECURITY_OFFICER');
    
    // Restablecer sistema - máximo nivel de permisos
    Route::post('/reset', [SettingsController::class, 'reset'])
        ->middleware('permission.all:RESET_SYSTEM,SUPER_ADMIN,CONFIRM_RESET');
});

// ===================================================================
// EJEMPLO 6: Gestión de Roles y Permisos (Meta-permisos)
// ===================================================================
Route::prefix('admin/roles')->middleware('jwt.auth')->group(function () {
    // Ver roles - permiso básico
    Route::get('/', [RoleController::class, 'index'])
        ->middleware('permission:VIEW_ROLES');
    
    // Crear/editar roles - permiso de gestión
    Route::post('/', [RoleController::class, 'create'])
        ->middleware('permission:MANAGE_ROLES');
    Route::put('/{id}', [RoleController::class, 'update'])
        ->middleware('permission:MANAGE_ROLES');
    
    // Asignar permisos a roles - requiere permisos elevados
    Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions'])
        ->middleware('permission.all:MANAGE_ROLES,ASSIGN_PERMISSIONS');
    
    // Eliminar roles - máximo cuidado
    Route::delete('/{id}', [RoleController::class, 'delete'])
        ->middleware('permission.all:DELETE_ROLES,ADMIN_ACCESS,CONFIRM_DELETE');
});

// ===================================================================
// EJEMPLO 7: Operaciones Financieras
// ===================================================================
Route::prefix('finance')->middleware('jwt.auth')->group(function () {
    // Ver transacciones propias
    Route::get('/my-transactions', [FinanceController::class, 'myTransactions'])
        ->middleware('permission:VIEW_OWN_TRANSACTIONS');
    
    // Ver todas las transacciones
    Route::get('/transactions', [FinanceController::class, 'allTransactions'])
        ->middleware('permission:VIEW_ALL_TRANSACTIONS');
    
    // Crear transacciones menores
    Route::post('/small-transaction', [FinanceController::class, 'createSmall'])
        ->middleware('permission:CREATE_TRANSACTION');
    
    // Crear transacciones mayores - requiere aprobación
    Route::post('/large-transaction', [FinanceController::class, 'createLarge'])
        ->middleware('permission.all:CREATE_TRANSACTION,APPROVE_LARGE_AMOUNT');
    
    // Revertir transacción - muy restringido
    Route::post('/revert/{id}', [FinanceController::class, 'revert'])
        ->middleware('permission.all:REVERT_TRANSACTION,FINANCE_ADMIN,AUDIT_APPROVED');
});

// ===================================================================
// EJEMPLO 8: Sistema de Contenido (CMS)
// ===================================================================
Route::prefix('content')->middleware('jwt.auth')->group(function () {
    // Crear borradores - todos los editores
    Route::post('/drafts', [ContentController::class, 'createDraft'])
        ->middleware('permission:CREATE_CONTENT');
    
    // Editar propio contenido
    Route::put('/drafts/{id}', [ContentController::class, 'updateDraft'])
        ->middleware('permission.any:EDIT_OWN_CONTENT,EDIT_ALL_CONTENT');
    
    // Publicar contenido - requiere revisor o editor senior
    Route::post('/publish/{id}', [ContentController::class, 'publish'])
        ->middleware('permission.any:PUBLISH_CONTENT,EDITOR_SENIOR');
    
    // Despublicar - requiere editor senior o admin
    Route::post('/unpublish/{id}', [ContentController::class, 'unpublish'])
        ->middleware('permission.any:UNPUBLISH_CONTENT,ADMIN_ACCESS');
    
    // Eliminar permanentemente - máxima precaución
    Route::delete('/{id}', [ContentController::class, 'delete'])
        ->middleware('permission.all:DELETE_CONTENT,ADMIN_ACCESS,CONFIRM_DELETE');
});

// ===================================================================
// EJEMPLO 9: Auditoría y Logs
// ===================================================================
Route::prefix('audit')->middleware('jwt.auth')->group(function () {
    // Ver logs propios - todos los usuarios
    Route::get('/my-logs', [AuditController::class, 'myLogs'])
        ->middleware('permission:VIEW_OWN_LOGS');
    
    // Ver logs del sistema - auditores
    Route::get('/system-logs', [AuditController::class, 'systemLogs'])
        ->middleware('permission:VIEW_SYSTEM_LOGS');
    
    // Exportar logs - requiere autorización
    Route::post('/export', [AuditController::class, 'export'])
        ->middleware('permission.all:EXPORT_LOGS,AUDITOR_ACCESS');
    
    // Limpiar logs antiguos - solo administradores
    Route::delete('/purge', [AuditController::class, 'purge'])
        ->middleware('permission.all:PURGE_LOGS,ADMIN_ACCESS,CONFIRM_PURGE');
});

// ===================================================================
// EJEMPLO 10: API de Integración Externa
// ===================================================================
Route::prefix('api/external')->middleware('jwt.auth')->group(function () {
    // Leer datos - API básica
    Route::get('/data', [ExternalApiController::class, 'getData'])
        ->middleware('permission:API_READ');
    
    // Enviar datos - API con escritura
    Route::post('/data', [ExternalApiController::class, 'sendData'])
        ->middleware('permission.all:API_WRITE,API_AUTHORIZED');
    
    // Sincronización completa - operación crítica
    Route::post('/sync', [ExternalApiController::class, 'sync'])
        ->middleware('permission.all:API_SYNC,ADMIN_ACCESS,CONFIRM_SYNC');
});

/**
 * NOTA: Para activar estos ejemplos en tu aplicación:
 * 
 * 1. Descomentar las rutas que desees usar
 * 2. Crear los permisos correspondientes en la base de datos
 * 3. Asignar los permisos a los roles apropiados
 * 4. Probar con usuarios de diferentes roles
 */
