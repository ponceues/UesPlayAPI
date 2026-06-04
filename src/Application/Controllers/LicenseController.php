<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Services\LicenseService;
use UesPlay\Domain\Mappers\LicenseMapper;


class LicenseController extends Controller
{
    private readonly LicenseService $licenseService;
    
    public function __construct(LicenseService $licenseService) {
        $this->licenseService = $licenseService;
    }
    
    public function listLicenses(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->licenseService->fetch($filter);
        
        return response()->json($res->toArray());
    }
    
    public function search(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        $res = $this->licenseService->search($filter);
        
        return response()->json($res->toArray());
    }
    
    public function create(Request $request):JsonResponse{
        $licence = LicenseMapper::fromRequestToEntity($request, false);
        $res = $this->licenseService->create($licence);
        
        return response()->json($res->toArray());
    }
    
    public function update(Request $request):JsonResponse{
        $licence = LicenseMapper::fromRequestToEntity($request, true);
        $res = $this->licenseService->update($licence);
        
        return response()->json($res->toArray());
    }
    
    
    public function delete(Request $request, string $licenceId):JsonResponse{
        $res = $this->licenseService->trash($licenceId);
        
        return response()->json($res);
    }
    
    public function fetchSummary():JsonResponse{
        $res = $this->licenseService->summaryLicenses();        
        return response()->json($res);
    }
    
}

