<?php
namespace UesPlay\Application\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use UesPlay\Domain\Services\LanguageService;
use UesPlay\Domain\Mappers\FilterMapper;

class LanguageController extends Controller
{
    private readonly LanguageService $languageService;
    
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }
    
    public function fetch(Request $request): JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->languageService->fetch($filter);
        return response()->json($res->toArray());
    }
}

