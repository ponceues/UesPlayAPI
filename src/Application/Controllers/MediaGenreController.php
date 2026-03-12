<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\MediaGenreService;
use UesPlay\Domain\Mappers\MediaGenreMapper;

class MediaGenreController extends Controller
{
    private readonly MediaGenreService $mediaGenreService;

    public function __construct(MediaGenreService $mediaGenreService) {
        $this->mediaGenreService = $mediaGenreService;
    }

    public function fetch(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->mediaGenreService->fetch($filter);
        return response()->json($res->toArray());
    }

    public function search(Request $request, $mediaTypeId): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->mediaGenreService->search($filter, $mediaTypeId);
        return response()->json($res->toArray());
    }

    public function create(Request $request): JsonResponse {
        $mediaGenre = MediaGenreMapper::fromRequestToEntity($request, false);
        $res = $this->mediaGenreService->create($mediaGenre);
        return response()->json($res->toArray());
    }

    public function update(Request $request, string $mediaGenreId): JsonResponse {
        $mediaGenre = MediaGenreMapper::fromRequestToEntity($request, true);
        $res = $this->mediaGenreService->update($mediaGenre);
        return response()->json($res->toArray());
    }

    public function delete(Request $request,string $mediaTypeId,  string $mediaGenreId): JsonResponse {
        $res = $this->mediaGenreService->trash($mediaGenreId);
        return response()->json($res);
    }
}