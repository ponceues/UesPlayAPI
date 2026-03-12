<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Mappers\FilterMapper;
use UesPlay\Domain\Mappers\DeviceMapper;
use UesPlay\Domain\Services\DeviceService;


class DeviceController extends Controller {
    
    private readonly DeviceService $deviceService;

    public function __construct(DeviceService $deviceService) {
        $this->deviceService = $deviceService;
    }

    public  function fetchDevices(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->deviceService->fetchDevices($filter);
        return response()->json($res->toArray());
    }
    
    public  function fetchForAdmin(Request $request):JsonResponse{
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->deviceService->fetchDevicesForAdmin($filter);
        return response()->json($res->toArray());
    }
    
    public  function createDevice(Request $request):JsonResponse{
        $device = DeviceMapper::fromRequestToEntity($request, false);
        
        $res = $this->deviceService->createDevice($device);
        return response()->json($res->toArray());
    }
    
    public  function updateDevice(Request $request, string $deviceId):JsonResponse{
        $device = DeviceMapper::fromRequestToEntity($request, true);
        $res = $this->deviceService->updateDevice($device);
        return response()->json($res->toArray());
    }
    
    public  function deleteDevice(string $deviceId):JsonResponse{
        $res = $this->deviceService->deleteDevice($deviceId);
        return response()->json($res);
    }
    
    public  function findDevice(string $deviceId):JsonResponse{
        $res = $this->deviceService->findDevice($deviceId);
        return response()->json($res->toArray());
    }
}
