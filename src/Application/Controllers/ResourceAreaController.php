<?php

namespace UesPlay\Application\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\AreaMapper;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\ResourceAreaService;


class ResourceAreaController extends Controller {
    
    
    private readonly ResourceAreaService $areaService;
    
    public function __construct(ResourceAreaService $areaService) {
        $this->areaService = $areaService;
    }
    
    public function fetchForView(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->areaService->fetchForView($filter);
        
        return response()->json($res->toArray());
    }
    
    public function fetchAreas(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->areaService->fetchByFilter($filter);
        
        return response()->json($res->toArray());
    }
    
    public function createArea(Request $request):JsonResponse{
        $area = AreaMapper::fromRequestToEntity($request, false);
        $res = $this->areaService->createArea($area);
        
        return response()->json($res->toArray());
    }
    
    public function updateArea(Request $request):JsonResponse{
        $area = AreaMapper::fromRequestToEntity($request, true);
        $res = $this->areaService->updateArea($area);
        return response()->json($res->toArray());
    }
    
    public function deleteArea(string $areaId) {
        $res = $this->areaService->deleteArea($areaId);
        return response()->json(['estado'=>$res]);
    }
}
