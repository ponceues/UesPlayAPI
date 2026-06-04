<?php

namespace UesPlay\Application\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Mappers\ResourceTypeMapper;
use UesPlay\Domain\Services\ResourceTypeService;

class ResourceTypesController extends Controller {
    
    private readonly ResourceTypeService $resourceTypeService;


    public function __construct(ResourceTypeService $resourceTypeService) {
        $this->resourceTypeService = $resourceTypeService;
    }
    
    public function listResourceTypes(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->resourceTypeService->listResourceTypes($filter);
        
        return response()->json($res->toArray());
    }
    
    public function fetchResourceTypes(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->resourceTypeService->fetchResourceTypes($filter);
        
        return response()->json($res->toArray());
    }
    
    public function createResourceType(Request $request):JsonResponse{
        $resourceType = ResourceTypeMapper::fromRequestToEntity($request, false);
        $res = $this->resourceTypeService->createResourceType($resourceType);
        
        return response()->json($res->toArray());
    }
    
    public function updateResourceType(Request $request):JsonResponse{
        $resourceType = ResourceTypeMapper::fromRequestToEntity($request, true);
        $res = $this->resourceTypeService->updateResourceType($resourceType);
        return response()->json($res->toArray());
    }
    
    public function deleteResourceType(string $typeId) {
        $res = $this->resourceTypeService->delelteResourceType($typeId);
        return response()->json(['estado'=>$res]);
    }
}
