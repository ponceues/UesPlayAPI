<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\ResourceStateService;

class ResourceStateController extends Controller {
    private readonly ResourceStateService $resourceService;
    
    public function __construct(ResourceStateService $resourceService) {
        $this->resourceService = $resourceService;
    }

    public function fetchResourceStates(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->resourceService->fetchStates($filter);
        
        return response()->json($res->toArray());
    }

}
