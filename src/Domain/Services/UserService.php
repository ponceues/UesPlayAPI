<?php

namespace UesPlay\Domain\Services;

use Illuminate\Http\UploadedFile;
use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;

use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\BadRequestException;


use UesPlay\Domain\Entities\User;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Entities\UserState;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IUserRepository;
use UesPlay\Domain\Interfaces\IUserStateRepository;
use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Interfaces\IUserAreasRepository;
use UesPlay\Domain\Interfaces\IRolRepository;

class UserService {
    private readonly IUserRepository $userRepository;
    private readonly IUserStateRepository $stateRepository;
    private readonly EmailService   $emailService;
    private readonly IAreaRepository $areaRepository;
    private readonly IRolRepository $rolRepository;
    

    public function __construct(
            IUserRepository $userRepository, 
            IUserStateRepository $stateRepository,
            IAreaRepository $areaRepository,
            IUserAreasRepository $userAreasRepository,
            IRolRepository $rolRepository,
            EmailService $emailService) {
        $this->userRepository = $userRepository;
        $this->stateRepository = $stateRepository;
        $this->areaRepository = $areaRepository;
        $this->userAreasRepository = $userAreasRepository;
        $this->rolRepository =$rolRepository;
        $this->emailService = $emailService;        
    }
         
    public function fetch(Filter $filter):Envelop {
        try{
            $result = new Envelop();
            $count = $this->userRepository->countByFilter($filter);
            $users = $this->userRepository->searchByFilter($filter);
            
            $stateFilter = new Filter();
            $stateFilter->setPageSize(100);
            $states = $this->stateRepository->fetch($stateFilter);
            
            $users->each(function(User $user) use ($states) {
                $state = $states->first(function (UserState $state) use ($user) {
                    return $state->getStateId() === $user->getStateId();
                });
                $areas = $this->areaRepository->fetchByUser($user->getUserId());
                $user->setState($state);
                $user->setAreas($areas);
            });
            
            $result->setData($users, $filter, $count,'users');
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function register(User $user):User{
        try{
            $filter = new Filter();
            $filter->setText($user->getEmail());
            if($this->userRepository->countByFilter($filter) > 0){
                throw(new BadRequestException("El correo ya se cuentra registrado."));
            }
            
            $state = $this->stateRepository->findByCode('CREATED');
            $user->setUserId(Uuid::uuid4()->toString());
            $user->setState($state);
            
            $user->setCreatedAt(Carbon::now('utc'));
            $user->setUpdatedAt(Carbon::now('utc'));
            
            $encrypt =  Hash::make($user->getPassword());
            
            $user->setPassword($encrypt);
            
            $templateCode = 'REGISTERUSER';
            $this->emailService->sendEmail($templateCode, $user->getEmail(), collect());
            
            $result = $this->userRepository->insert($user);            
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
   
    public function createSingleUser(User $user):User{
        
        try{
            $filter = new Filter();
            $filter->setEmail($user->getEmail());
            if($this->userRepository->countByFilter($filter) > 0){
                throw(new BadRequestException("El correo ya se cuentra registrado."));
            }
            $encrypt =  Hash::make('secret$2025');
            $state = $this->stateRepository->findByCode('CREATED');
            
            $user->setUserId(Uuid::uuid4()->toString());
            $user->setState($state);
            $user->setCreatedAt(Carbon::now('utc'));
            $user->setUpdatedAt(Carbon::now('utc'));
            $user->setPassword($encrypt);
            $code = Str::random(64);
            $this->userRepository->insertWithCode($user,$code);
            
            //insertamos las areas
            $areasCodes = $user->getAreasCodes();
            foreach($areasCodes as $areaId){
                $pivotArea = $this->areaRepository->findById($areaId);
                if($pivotArea !== null){
                    $this->userRepository->addArea($user->getUserId(), $pivotArea->getAreaId(),Carbon::now('utc'));
                }
            }
            $data = collect([
                    "{{imagenUrl}}" => config('app.front_url') . "/svg/minerva.svg",
                    "{{userEmail}}" => $user->getEmail(),
                    "{{frontUrl}}" => config('app.front_url') . "/auth/verify-account",
                    "{{identity}}" => base64_encode($user->getUserId()),
                    "{{code}}"  =>$code
            ]);
            $this->emailService->sendEmail("VERIFY_ACC", $user->getEmail(), $data);
            return $this->userRepository->findById($user->getUserId());
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function updateUser(User $user):User{
        try{
            
            $updateUser = $this->userRepository->findById($user->getUserId());
            
            $updateUser->setName($user->getName());
            $updateUser->setRolId($user->getRolId());
            $updateUser->setStateId($user->getStateId());
            $updateUser->setUpdatedAt(Carbon::now('utc'));
            
            $this->userRepository->updateUser($updateUser);
            
            // Sincronizamos las áreas del usuario . . . 
            $newAreasCodes = $user->getAreasCodes();
            
            // Primero deshabilitamos todas las áreas
            $this->userRepository->disableAllAreas($user->getUserId());
            
             
            // Ahora obtenemos las áreas actuales (ya deshabilitadas) - sin caché
            $currentAreas = $this->areaRepository->fetchFullByUser($user->getUserId());
            
            // Convertimos currentAreas a array si es una colección
            $currentAreasArray = is_array($currentAreas) ? $currentAreas : iterator_to_array($currentAreas);
            
            foreach($newAreasCodes as $areaId){
                $pivotArea = $this->areaRepository->findById($areaId);
                if($pivotArea !== null){
                    // Buscamos si el área ya existe en las áreas actuales
                    $exist = null;
                    foreach($currentAreasArray as $currentArea){
                        if($currentArea->getAreaId() === $pivotArea->getAreaId()){
                            $exist = $currentArea;
                            break;
                        }
                    }
                    
                    if($exist === null){
                        // Si no existe, agregamos el área
                        $this->userRepository->addArea($user->getUserId(), $pivotArea->getAreaId(), Carbon::now('utc'));
                    }else{
                        // Si existe, solo la activamos
                        $this->userRepository->activateArea($user->getUserId(), $pivotArea->getAreaId()); 
                    }
                }
            }
            return $this->userRepository->findById($user->getUserId());
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function generateBulkTemplate():string{
        try{
            $filename = 'PlantillaUsusarios.xlsx';
            $spreadsheet = new Spreadsheet();

            $activeWorksheet = $spreadsheet->getActiveSheet();
            $activeWorksheet->setCellValue('A1', 'Usuario');
            $activeWorksheet->setCellValue('B1', 'Correo');
            $activeWorksheet->setCellValue('C1', 'CodigoRol');
            $activeWorksheet->setCellValue('D1', 'CodigoArea');
            
            $range = "A1:B1";

            $activeWorksheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $activeWorksheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $activeWorksheet->getStyle($range)->getFont()->setBold(true);
            $activeWorksheet->getStyle($range)->getFont()->setBold(true);
            
            
            foreach (range('A', 'Z') as $col) {
                $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
            }
            $path = storage_path("app/exports/{$filename}");

            $writer = new Xlsx($spreadsheet);
            $writer->save($path);

            return $path;
        } catch (Exception $ex){
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }
    
    public function bulkCreateUsers(UploadedFile $file,string $rolId,string $areaId){
        try{
            $errorCount = 0;
            $warnCount = 0;
            $successCount = 0;
            $userList = collect();
            $toProccessLst = collect();
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $hoja = $spreadsheet->getActiveSheet();
            $filas = $hoja->toArray();
            
            $isHeader = true;
            foreach ($filas as $fila)  {
                // para cada fila se debe validar los datos del usuario;
                if($isHeader){
                    $isHeader = false;
                    continue;
                }
                $username = $fila[0].'';
                $email = $fila[1].'';
                // Cada $fila es un array de celdas (A, B, C...)
                
                $user = new User();
                $user->setRolId($rolId);
                $user->setAreaId($areaId);
                $user->setEmail($email);
                $user->setName($username);
                

                $userList->push($user);
            }   
            
            if($userList->count() < 1 ){
                throw new BadRequestException("El archivo se encuentra vacio");
            }
            //Validamos cada usuario para validar 
            foreach ($userList as $user){
                if( 
                    filter_var($user->getEmail(),FILTER_VALIDATE_EMAIL) &&  
                    $user->getName() !== null && 
                    strlen($user->getName()) > 3 
                ){
                    $toProccessLst->push($user);
                }
            };
            
            /* Processamos la lista de usuarios
             * para cada uno debemos insertarlo si no existe 
             * y agregamos los valores de las areas si es necesario
             */
            
            $rol = $this->rolRepository->findById($rolId);
            $area = $this->areaRepository->findById($areaId);
            $state = $this->stateRepository->findByCode('CREATED');
            foreach($toProccessLst as $user){
                try {
                    $pivotUser = $this->userRepository->findByEmail($user->getEmail());
                    if($pivotUser !== null){
                        //verificamos que el area no este en la lista de areas del usuario
                        $areas = $this->areaRepository->fetchByUser($pivotUser->getUserId());
                        $existList = $areas->filter(function (Area $a) use ($area){
                            return $a->getAreaId() === $area->getAreaId();
                        });
                        if($existList->count() === 0){
                            //si el area no existe para el usuario se la agregamos.
                            $this->userRepository->addArea($pivotUser->getUserId(), $area->getAreaId(),Carbon::now('utc'));
                        }
                        $successCount++;
                    }else{
                        //creamos el usuario
                        $user->setUserId(Uuid::uuid4()->toString());
                        $user->setRolId($rol->getRolId());
                        $user->setStateId($state->getStateId());
                        $user->setState($state);
                        $user->setUpdatedAt(Carbon::now('utc'));
                        $user->setCreatedAt(Carbon::now('utc'));

                        $encrypt =  Hash::make('UesPlay$2025');
                        $user->setPassword($encrypt);                        
                        $code = Str::random(64);
                        
                        $this->userRepository->insertWithCode($user,$code);
                        //agregamos el area
                        $this->userRepository->addArea($user->getUserId(), $area->getAreaId(),Carbon::now('utc'));

                        //realizamos el envio del email de confirmacion.
                        $data = collect([
                                "{{imagenUrl}}" => config('app.front_url') . "/images/img03.jpg",
                                "{{userEmail}}" => $user->getEmail(),
                                "{{frontUrl}}" => config('app.front_url') . "/auth/verify-account",
                                "{{identity}}" => base64_encode($user->getUserId()),
                                "{{code}}"  =>$code
                        ]);
                        $this->emailService->sendEmail("VERIFY_ACC", $user->getEmail(), $data);
                        $successCount++;
                    }
                } catch (Exception $ex){
                    $errorCount++;
                }

            }
            
            $res = [
                "error" =>  $errorCount,
                "success"   =>  $successCount,
                "warn"  =>$warnCount
            ];
            
            return $res;
            
        }catch (BadRequestException $ex){
            throw $ex;
        } catch (Exception $ex){
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}
