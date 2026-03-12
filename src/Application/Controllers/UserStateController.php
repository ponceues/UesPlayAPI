<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;

use UesPlay\Domain\Services\UserStateService;

class UserStateController extends Controller {
    private readonly UserStateService $userStateService;
    
    public function __construct(UserStateService $userStateService) {
        $this->userStateService = $userStateService;
    }
    
    public function fetchUserStates(Request $request): JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->userStateService->fetchUserStates($filter);
        
        return response()->json($res->toArray());
    }

}
