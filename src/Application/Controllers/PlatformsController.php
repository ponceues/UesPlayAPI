<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\PlatformMapper;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\PlatformService;

class PlatformsController extends Controller {
    private readonly PlatformService $platformService;
    
    public function __construct(PlatformService $platformService) {
        $this->platformService = $platformService;
    }

    public  function listPlatforms(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->platformService->listPlatforms($filter);
        return response()->json($res->toArray());
    }
    
    public  function fetchPlatforms(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->platformService->fetchPlatforms($filter);
        return response()->json($res->toArray());
    }
    
    public  function fetch(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->platformService->fetch($filter);
        return response()->json($res->toArray());
    }

    public  function create(Request $request):JsonResponse{
        
        $entity = PlatformMapper::fromRequestToEntity($request,false);
        
        $res = $this->platformService->create($entity);
        return response()->json($res->toArray());
    }

    public  function find(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->platformService->fetchPlatforms($filter);
        return response()->json($res->toArray());
    }    
    public  function update(Request $request,string $platformId):JsonResponse{
        $entity = PlatformMapper::fromRequestToEntity($request, true);
        $res = $this->platformService->update($entity);
        return response()->json($res->toArray());
    }
    
    public  function delete(string $platformId):JsonResponse{
       
        $res = $this->platformService->delete($platformId);
        return response()->json($res);
    }    
    
    public  function fetchSummary():JsonResponse{
        
        $res = $this->platformService->summaryPlatforms();
        return response()->json($res);
    } 
    
}
