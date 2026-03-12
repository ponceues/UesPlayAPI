<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Services\MenuService;

class MenuController extends Controller{
    private readonly MenuService $menuService;
    
    public function __construct(MenuService $menuService) {
        $this->menuService = $menuService;
    }
    
    public function fetchPermission():JsonResponse{
        $res = $this->menuService->fetchMenus();        
        return response()->json($res->toArray());
    }

}
