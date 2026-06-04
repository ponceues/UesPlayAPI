<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Mappers\RolMapper;
use UesPlay\Domain\Services\RolService;

class RolController extends Controller {
    private RolService $rolService;
    
    public function __construct(RolService $rolService) {
        $this->rolService = $rolService;
    } 
    
    public function fetchForView(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->rolService->fetchForView($filter);
        
        return response()->json($res->toArray());
    }
    
    public function fetchRoles(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->rolService->fetchRoles($filter);
        
        return response()->json($res->toArray());
    }
    
    public function findRol(Request $request, string $rolId){
        $res = $this->rolService->findRolById($rolId);
        
        return response()->json($res->toArray());
    }
    
    public function createRol(Request $request):JsonResponse{
        $rol = RolMapper::fromRequestToEntity($request,false);
        $res = $this->rolService->createRol($rol);
        
        return response()->json($res->toArray());
    }
    
    public function updateRol(Request $request):JsonResponse{
        $rolToUpdate = RolMapper::fromRequestToEntity($request, true);
        
        $res = $this->rolService->updateRol($rolToUpdate);
        
        return response()->json($res->toArray());
    }
    
    public function deleteRol(string $rolId):JsonResponse{
        
        $result = $this->rolService->delteRol($rolId);
        
        return response()->json(['estado'=>$result]);
    }
    
    public function addMenuToRol(string $rolId,string $menuId):JsonResponse{
        $result = $this->rolService->addMenuToRol($rolId, $menuId);
        return response()->json(['estado'=>$result]);
    }
    
    public function removeMenuFromRol(string $rolId, string $menuId) {
        $result = $this->rolService->removeMenuFromRol($rolId, $menuId);
        return response()->json(['estado'=>$result]);
    }

    public function removePermission(string $rolId, string $permissionId) {
        $result = $this->rolService->removePermissionFromRol($rolId, $permissionId);
        return response()->json(['estado'=>$result]);
    }

    public function addPermission(string $rolId,string $permissionId):JsonResponse{
        $result = $this->rolService->addPermissionToRol($rolId, $permissionId);
        return response()->json(['estado'=>$result]);
    }
    
    public function rolesSummary():JsonResponse{
        $result = $this->rolService->getRolSummary();
        return response()->json($result);
    }
    
}
