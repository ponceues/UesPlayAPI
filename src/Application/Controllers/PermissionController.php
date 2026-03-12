<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Services\PermissionServices;


class PermissionController extends Controller {
    
    private readonly PermissionServices $permissionService;
    
    public function __construct(PermissionServices $permissionService) {
        $this->permissionService = $permissionService;
    }
    
    public function fetchPermissions():JsonResponse{
        $res = $this->permissionService->fetchPermission();
        
        return response()->json($res->toArray());
    }
}
