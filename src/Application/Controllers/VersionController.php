<?php
namespace UesPlay\Application\Controllers;
use UesPlay\Domain\Services\ResourceVersionService;
use Illuminate\Http\Request;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Mappers\VersionMapper;

class VersionController
{
    private readonly ResourceVersionService $resourceVersionService;
    
    public function __construct(ResourceVersionService $resourceVersionService)
    {
        $this->resourceVersionService = $resourceVersionService;
    }
    
    public function fetchVersions(Request $request, string $resourceId)
    {
        $filter =  FilterMapper::fromRequestToEntity($request);
        $res = $this->resourceVersionService->fetchByResource($resourceId, $filter);
        return response()->json($res->toArray());
    }
    
    public function createVersion(Request $request, string $resourceId)
    {
        $version =  VersionMapper::fromRequestToEntity($request, false);
        $file = $request->file('source');
        $res = $this->resourceVersionService->createVersion($resourceId,$version, $file);
        return response()->json($res->toArray());
    }
 
    public function dowloadVersion(Request $request, string $resourceId, string $versionId)
    {
        return $this->resourceVersionService->dowloadVersion($resourceId, $versionId, true);
    }
    
    public function donwloadVersionFile(Request $request, string $resourceId, string $versionId)
    {
        return $this->resourceVersionService->dowloadVersion($resourceId, $versionId, false);
    }
}