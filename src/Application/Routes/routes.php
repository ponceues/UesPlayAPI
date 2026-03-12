<?php
use Illuminate\Support\Facades\Route;
use UesPlay\Application\Controllers\RolController;
use UesPlay\Application\Controllers\PermissionController;
use UesPlay\Application\Controllers\MenuController;
use UesPlay\Application\Controllers\UserController;
use UesPlay\Application\Controllers\ResourceAreaController;
use UesPlay\Application\Controllers\ResourceStateController;
use UesPlay\Application\Controllers\ResourceTypesController;
use UesPlay\Application\Controllers\PlatformsController;
use UesPlay\Application\Controllers\DeviceController;
use UesPlay\Application\Controllers\AuthController;
use UesPlay\Application\Controllers\UserStateController;
use UesPlay\Application\Controllers\ResourceController;
use UesPlay\Application\Controllers\LanguageController;
use UesPlay\Application\Controllers\VersionController;
use UesPlay\Application\Controllers\LicenseController;
use UesPlay\Application\Controllers\MediaTypeController;
use UesPlay\Application\Controllers\MediaGenreController;
use UesPlay\Application\Controllers\CommentController;

Route::prefix('uesplay/catalogs')->group(function () {
    Route::get('platforms',[PlatformsController::class,'fetchPlatforms']);
    Route::get('devices',[DeviceController::class,'fetchDevices']);
    Route::get('languages',[LanguageController::class,'fetch']);
    Route::get('resourceStates',[ResourceStateController::class,'fetchResourceStates']);
    Route::get('resources',[ResourceController::class,'search']);
    Route::get('resources/{resourceId}',[ResourceController::class,'findForView']);
    Route::get('licences',[LicenseController::class,'fetch']);
    Route::get('media-types',[MediaTypeController::class,'fetch']);
    Route::get('media-genres',[MediaGenreController::class,'fetch']);
    
});

Route::prefix('resources')->group(function () {
    Route::get('',[ResourceController::class,'search']);
    Route::get('/{resourceId}',[ResourceController::class,'findForView']);
    Route::get('/{resourceId}/comments',[CommentController::class,'fetch']);
    Route::post('/{resourceId}/comments',[CommentController::class,'create']);
    Route::get('/{resourceId}/versions/{versionId}/download',[VersionController::class,'dowloadVersion']);
});

Route::prefix('public/catalogs')->group(function () {
    Route::get('resourceStates',[ResourceStateController::class,'fetchResourceStates']);
    
    Route::post('register',[UserController::class,'registerGuestUser']);
    Route::get('resource-types',[ResourceTypesController::class,'fetchResourceTypes']);
});

Route::prefix('auth')->group(function () {
    Route::post('login',[AuthController::class,'login']);
    Route::post('verifyaccount',[AuthController::class,'verifyAccount']);
    Route::post('recoveryrequest',[AuthController::class,'sendRecoveryEmail']);
    Route::post('recoverypassword',[AuthController::class,'recoveryAccount']);
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('userSettings',[AuthController::class,'getUsersSettings']);
    });
    
    /**
     * EJEMPLOS DE USO DE MIDDLEWARES DE PERMISOS:
     * 
     * 1. Un solo permiso requerido:
     *    ->middleware('permission:CREATE_USER')
     * 
     * 2. Al menos uno de varios permisos (OR):
     *    ->middleware('permission.any:CREATE_USER,UPDATE_USER')
     * 
     * 3. Todos los permisos requeridos (AND):
     *    ->middleware('permission.all:VIEW_USERS,MANAGE_ROLES')
     * 
     * 4. Combinar con otros middlewares:
     *    ->middleware(['jwt.auth', 'permission:DELETE_USER'])
     */
    
    Route::prefix('admin')->group(function () {
        Route::prefix('catalogs')->group(function () {
            Route::get('areas',[ResourceAreaController::class,'fetchForView']);
            Route::get('roles',[RolController::class,'fetchForView']);
        });
        
        
        // Management roles . . .
        Route::prefix('roles')->group(function () {
            Route::get('',[RolController::class,'fetchRoles'])->middleware('permission:can.list.roles');
            Route::post('',[RolController::class,'createRol'])->middleware('permission:can.create.roles');
            Route::get('/{rolId}',[RolController::class,'findRol'])->middleware('permission:can.list.roles');
            Route::put('/{rolId}',[RolController::class,'updateRol'])->middleware('permission:can.update.roles');
            Route::delete('/{rolId}',[RolController::class,'deleteRol'])->middleware('permission:can.delete.roles');
            Route::delete('/{rolId}/permissions/{permissionId}',[RolController::class,'removePermission'])->middleware('permission:can.manage.permissions.roles');
            Route::post('/{rolId}/permissions/{permissionId}',[RolController::class,'addPermission'])->middleware('permission:can.manage.permissions.roles');
            Route::post('/{rolId}/menus/{menuId}',[RolController::class,'addMenuToRol'])->middleware('permission:can.manage.menus.roles');
            Route::delete('/{rolId}/menus/{menuId}',[RolController::class,'removeMenuFromRol'])->middleware('permission:can.manage.menus.roles');
        });
        
        //Devices management . . .
        Route::prefix('devices')->group(function () {
            Route::get('',[DeviceController::class,'fetchForAdmin'])->middleware('permission:can.list.devices');
            Route::post('',[DeviceController::class,'createDevice'])->middleware('permission:can.create.device');
            Route::get('{deviceId}',[DeviceController::class,'findDevice'])->middleware('permission:can.list.devices');
            Route::post('{deviceId}',[DeviceController::class,'updateDevice'])->middleware('permission:can.update.device');
            Route::delete('{deviceId}',[DeviceController::class,'deleteDevice'])->middleware('permission:can.delete.device');
        });
        
        //Manejo de plataformas . . . 
        Route::prefix('platforms')->group(function () {
            Route::get('',[PlatformsController::class,'fetch'])->middleware('permission:can.list.platforms');
            Route::post('',[PlatformsController::class,'create'])->middleware('permission:can.create.platform');
            Route::get('{platformId}',[PlatformsController::class,'find'])->middleware('permission:can.list.platforms');
            Route::post('{platformId}',[PlatformsController::class,'update'])->middleware('permission:can.update.platform');
            Route::delete('{platformId}',[PlatformsController::class,'delete'])->middleware('permission:can.delete.platform');
        });
        
        //Manejo de licencias . . .
        Route::prefix('licenses')->group(function () {
            Route::get('',[LicenseController::class,'search'])->middleware('permission:can.search.licenses');
            Route::post('',[LicenseController::class,'create'])->middleware('permission:can.create.licenses');
            Route::post('{licenceId}',[LicenseController::class,'update'])->middleware('permission:can.update.licenses');
            Route::delete('{licenceId}',[LicenseController::class,'delete'])->middleware('permission:can.delete.licenses');
            });
                
        //Manejo de tipos multimedia . . .
        Route::prefix('mediatypes')->group(function () {
            Route::get('',[MediaTypeController::class,'search'])->middleware('permission:can.search.mediatypes');
            Route::post('',[MediaTypeController::class,'create'])->middleware('permission:can.create.mediatypes');
            Route::get('{mediaTypeId}',[MediaTypeController::class,'find'])->middleware('permission:can.search.mediatypes');
            Route::post('{mediaTypeId}',[MediaTypeController::class,'update'])->middleware('permission:can.update.mediatypes');
            Route::delete('{mediaTypeId}',[MediaTypeController::class,'delete'])->middleware('permission:can.delete.mediatypes');
            
            Route::get('{mediaTypeId}/genres',[MediaGenreController::class,'search'])->middleware('permission:can.search.mediatypes.genres');
            Route::post('{mediaTypeId}/genres',[MediaGenreController::class,'create'])->middleware('permission:can.create.mediatypes.genres');
            Route::post('{mediaTypeId}/genres/{mediaGenreId}',[MediaGenreController::class,'update'])->middleware('permission:can.update.mediatypes.genres');
            Route::delete('{mediaTypeId}/genres/{mediaGenreId}',[MediaGenreController::class,'delete'])->middleware('permission:can.delete.mediatypes.genres');            
        });

        //Manejo de recursos . . .
        Route::prefix('resources')->group(function () {
            Route::get('',[ResourceController::class,'fetch'])->middleware('permission:can.search.resources');
            Route::post('',[ResourceController::class,'create'])->middleware('permission:can.create.resources');
            Route::get('/{resourceId}',[ResourceController::class,'find'])->middleware('permission:can.search.resources');
            Route::post('/{resourceId}',[ResourceController::class,'update'])->middleware('permission:can.update.resources');
            Route::put('/{resourceId}',[ResourceController::class,'updateState'])->middleware('permission:can.update.resources.states');
            Route::get('/{resourceId}/authors',[ResourceController::class,'fetchAuthors'])->middleware('permission:can.search.resources.authors');
            Route::post('/{resourceId}/authors',[ResourceController::class,'addAuthor'])->middleware('permission:can.create.resources.authors');
            Route::delete('/{resourceId}/authors/{authorId}',[ResourceController::class,'removeAuthor'])->middleware('permission:can.remove.resources.authors');
            
            Route::get('/{resourceId}/files',[ResourceController::class,'fetchFiles'])->middleware('permission:can.search.resources.files');
            Route::post('/{resourceId}/files',[ResourceController::class,'addFile'])->middleware('permission:can.add.resources.files');
            Route::delete('/{resourceId}/files/{fileId}',[ResourceController::class,'removeFile'])->middleware('permission:can.remove.resources.files'); 
            
            Route::get('/{resourceId}/versions',[VersionController::class,'fetchVersions'])->middleware('permission:can.search.resources.versions');
            Route::post('/{resourceId}/versions',[VersionController::class,'createVersion'])->middleware('permission:can.create.resources.versions');
            Route::get('/{resourceId}/versions/{versionId}/download',[VersionController::class,'donwloadVersionFile'])->middleware('permission:can.download.resources.versions');
            
            Route::get('/{resourceId}/comments',[CommentController::class,'search'])->middleware('permission:can.search.resources.comments');
            Route::post('/{resourceId}/comments',[CommentController::class,'create'])->middleware('permission:can.create.resources.comments');
            Route::post('/{resourceId}/comments/{commentId}',[CommentController::class,'update'])->middleware('permission:can.update.resources.comments');
            Route::delete('/{resourceId}/comments/{commentId}',[CommentController::class,'delete'])->middleware('permission:can.delete.resources.comments');
        });
        
        //Manejo de tipos de recurso . . .
        Route::prefix('resource/types')->group(function () {
            Route::get('',[ResourceTypesController::class,'fetchResourceTypes'])->middleware('permission:can.search.resourcetypes');
            Route::post('',[ResourceTypesController::class,'createResourceType'])->middleware('permission:can.create.resourcetypes');
            Route::post('/{typeId}',[ResourceTypesController::class,'updateResourceType'])->middleware('permission:can.update.resourcetypes');
            Route::delete('/{typeId}',[ResourceTypesController::class,'deleteResourceType'])->middleware('permission:can.delete.resourcetypes');
        });
        
        //Management users . . .
        Route::prefix('users')->group(function () {
            Route::get('bulkUpload',[UserController::class,'generateBulkTemplate'])->middleware('permission:can.download.user.template');
            Route::post('bulkUpload',[UserController::class,'processBulkFile'])->middleware('permission:can.upload.users'); 
            Route::get('',[UserController::class,'fetchUsers'])->middleware('permission:can.search.users');
            Route::post('',[UserController::class,'createUser'])->middleware('permission:can.create.users');
            Route::post('/{userId}',[UserController::class,'updateUser'])->middleware('permission:can.update.users');
            
        });
        
        // Management resource areas . . .        
        Route::prefix('resource-areas')->group(function () {
            Route::get('',[ResourceAreaController::class,'fetchAreas'])->middleware('permission:can.search.areas');
            Route::post('',[ResourceAreaController::class,'createArea'])->middleware('permission:can.create.areas');
            Route::post('/{areaId}',[ResourceAreaController::class,'updateArea'])->middleware('permission:can.update.areas');
            Route::delete('/{areaId}',[ResourceAreaController::class,'deleteArea'])->middleware('permission:can.delete.areas');
        });

        // catalogs que no requieren permisos especiales . . .
        
        Route::prefix('user-states')->group(function () {
            Route::get('',[UserStateController::class,'fetchUserStates']);
        });
                
        Route::prefix('catalogs')->group(function () {
            Route::get('menus',[MenuController::class,'fetchPermission']);
            Route::get('permissions',[PermissionController::class,'fetchPermissions']);
        });
            

        
    });
});
