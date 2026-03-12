<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\MediaTypeService;
use UesPlay\Domain\Mappers\MediaTypeMapper;

class MediaTypeController extends Controller
{
    private readonly MediaTypeService $mediaTypeService;

    public function __construct(MediaTypeService $mediaTypeService) {
        $this->mediaTypeService = $mediaTypeService;
    }

    public function fetch(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->mediaTypeService->fetch($filter);
        return response()->json($res->toArray());
    }

    public function search(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->mediaTypeService->search($filter);
        return response()->json($res->toArray());
    }
    
    public function find(Request $request, string $mediaTypeId): JsonResponse {
        $res = $this->mediaTypeService->find($mediaTypeId);
        return response()->json($res->toArray());
    }

    public function create(Request $request): JsonResponse {
        $mediaType = MediaTypeMapper::fromRequestToEntity($request, false);
        $res = $this->mediaTypeService->create($mediaType);
        return response()->json($res->toArray());
    }

    public function update(Request $request): JsonResponse {
        $mediaType = MediaTypeMapper::fromRequestToEntity($request, true);
        $res = $this->mediaTypeService->update($mediaType);
        return response()->json($res->toArray());
    }

    public function delete(Request $request, string $mediaTypeId): JsonResponse {
        $res = $this->mediaTypeService->delete($mediaTypeId);
        return response()->json($res);
    }
}