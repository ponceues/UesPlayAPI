<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use UesPlay\Domain\Mappers\AuthorMapper;
use UesPlay\Domain\Mappers\FileMapper;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Mappers\ResourceMapper;
use UesPlay\Domain\Services\ResourceService;


class ResourceController extends Controller
{
    private readonly ResourceService $resourceService;
    
    public function __construct(ResourceService $resourceService) {
        $this->resourceService = $resourceService;
    }
    
    public function search(Request $request):JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $env = $this->resourceService->fetchForView($filter);
        return response()->json($env->toArray());
    }
    
    public function findForView(string $resourceId):JsonResponse
    {
        $res = $this->resourceService->findForView($resourceId);
        
        return response()->json($res->toArray());
    }
    
    
    public function fetch(Request $request):JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $env = $this->resourceService->fetchResources($filter);
        return response()->json($env->toArray());
    }
    
    public function create(Request $request):JsonResponse
    {
        $resource = ResourceMapper::fromRequestToEntity($request, false);
        $res = $this->resourceService->createResource($resource);
        
        return response()->json($res->toArray());
    }
    
    public function find(string $resourceId):JsonResponse
    {
        $res = $this->resourceService->findResource($resourceId);
        
        return response()->json($res->toArray());
    }
    
    public function update(Request $request, string $resourceId):JsonResponse
    {
        $resource = ResourceMapper::fromRequestToEntity($request, true);
        $res = $this->resourceService->update($resource);
        
        return response()->json($res->toArray());
    }
  
    public function updateState(Request $request, string $resourceId):JsonResponse
    {
        $resource = ResourceMapper::fromRequestToEntity($request, true);
        $res = $this->resourceService->updateState($resource);
        
        return response()->json($res->toArray());
    }
    
    
    public function addAuthor(Request $request, string $resourceId):JsonResponse
    {
        $author = AuthorMapper::fromRequestToEntity($request, false);
        $res = $this->resourceService->addResourceAuthor($author,$resourceId);
        return response()->json($res->toArray());
    }
    
    public function fetchAuthors(string $resourceId):JsonResponse
    {
         $res = $this->resourceService->fetchAuthors($resourceId);
        return response()->json($res->toArray());
    }

    public function removeAuthor(string $resourceId,string $authorId):JsonResponse
    {
        $res = $this->resourceService->deleteAuthor($resourceId, $authorId);
        return response()->json($res);
    }
    
    public function fetchFiles(string $resourceId):JsonResponse
    {
        $res = $this->resourceService->fetchFiles($resourceId);
        return response()->json($res->toArray());
    }
    
    public function addFile(Request $request, string $resourceId): JsonResponse
    {
        
        $resourceFile = FileMapper::fromRequestToEntity($request, false);
        $uploadedFile = $request->file('file');
        
        $res = $this->resourceService->addFile($uploadedFile, $resourceId, $resourceFile);
        return response()->json($res->toArray());
    }
    
    public function concatStrings(string $str1, string $str2, string $str3): JsonResponse
    {
        $concatenated = $str1 . $str2 . $str3;
        return response()->json(['result' => $concatenated]);
    }

    public function removeFile(string $resourceId, string $fileId):JsonResponse
    {
        $this->resourceService->removeFile($resourceId, $fileId);
        return response()->json(['result' => 'true']);
    }

    
}