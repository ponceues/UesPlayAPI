<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Services\UserService;
use UesPlay\Domain\Mappers\UserMapper;
use UesPlay\Domain\Mappers\FilterMapper;

class UserController extends Controller {
    private readonly UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function registerGuestUser(Request $request):JsonResponse {
        $userRequest = UserMapper::fromRequestToEntity($request, false);
        
        $res = $this->userService->register($userRequest);
        return response()->json($res->toArray());
    }
    
    public function fetchUsers(Request $request):JsonResponse {
        $filter = FilterMapper::fromRequestToEntity($request);
        
        $res = $this->userService->fetch($filter);
        return response()->json($res->toArray());
    }    
    
    public function createUser(Request $request):JsonResponse{
        $user = UserMapper::fromRequestToEntity($request, false);
        
        $res = $this->userService->createSingleUser($user);
        return response()->json($res->toArray());        
    }
    
    public function updateUser(Request $request, string $userId):JsonResponse{
        $user = UserMapper::fromRequestToEntity($request, true);
        
        $res = $this->userService->updateUser($user);
        return response()->json($res->toArray());
    }
    
    public function generateBulkTemplate(Request $request){
        
        $res = $this->userService->generateBulkTemplate();
        
        return response()->download($res)->deleteFileAfterSend(true);
    }
    
    public function processBulkFile(Request $request):JsonResponse{
        $validate = Validator::make(
            $request->all(),
            [
               
                'rolId'=>'required|uuid',
                'areaId'=>'required|uuid',
                'file'=>'file|extensions:xlsx'
            ],
            [
                'rolId.uuid'=>'El formato de {RolId} es invalido',
                'rolId.required'=>'El id del rol es requerido',
                'areaId.uuid'=>'El formato del id del rol es invalido',
                'areaid.required'=>'El id del area es requerido'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        
        $file = $request->file('file');
        $rolId = $request->string('rolId');
        $areaId = $request->string('areaId');
        
        $res = $this->userService->bulkCreateUsers($file,$rolId,$areaId);
        return response()->json($res);
    }
}
