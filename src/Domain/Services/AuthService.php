<?php

namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use Illuminate\Support\Facades\Hash;

use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Interfaces\IMenuRepository;
use UesPlay\Domain\Interfaces\IPermissionRepository;
use UesPlay\Domain\Interfaces\IUserStateRepository;
use UesPlay\Domain\Interfaces\IUserRepository;

class AuthService {
    private readonly IAreaRepository $areaRepository;
    private readonly IUserStateRepository $userStatesRepository;
    private readonly IMenuRepository $menuRepository;
    private readonly IPermissionRepository $permissionRepository;
    private readonly IUserRepository $userRepository;
    private readonly EmailService $emailService;

    public function __construct(
            IAreaRepository $areaRepository,
            IUserStateRepository $userStatesRepository,
            IPermissionRepository $permissionRepository,
            IMenuRepository $menuRepository,
            IUserRepository $userRepository,
            EmailService $emailService) {
        $this->areaRepository = $areaRepository;
        $this->userStatesRepository = $userStatesRepository;
        $this->permissionRepository = $permissionRepository;
        $this->menuRepository = $menuRepository;
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    public function validateUsersAttempt($credentials){
        try{
            $token = Auth::attempt($credentials);
            
            if (!$token) {
                $user = $this->userRepository->findByEmail($credentials['email'] ?? '');
                
                if ($user) {
                    $state = $this->userStatesRepository->findById($user->getStateId());
                    if ($state && $state->getCode() !== 'ACTIVE') {
                        throw new BadRequestException("Not authorized.");
                    }
                    throw new BadRequestException("Not authorized.");
                }
                throw new BadRequestException("Not authorized.");
            }
            
            return [
                'username'=>auth()->user()->name,  
                'token'=>$token
            ];
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function getUserInformation(){

        try{
            $user = auth()->user();

            $areas = $this->areaRepository->fetchByUser($user->user_id);

            $state = $this->userStatesRepository->findById($user->state_id);

            $menus = $this->menuRepository->fetchByRol($user->rol_id);
            $permissions = $this->permissionRepository->fetchByRol($user->rol_id);
            
            $result = [
                'userId'=>$user->user_id,
                'userName'=>$user->name,
                'email'=>$user->email,
                'areas'=>$areas->toArray(),
                'state'=>$state->toArray(),
                'menus'=>$menus->toArray(),
                'permissions'=>$permissions->toArray()
            ];
            
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function verifyAccount(string $identity,string $code, string $password): array{
        try {
            $userIdentity =  base64_decode($identity);
            $encrypt =  Hash::make($password);
            $user = $this->userRepository->verfifyAccount($userIdentity, $code);
            if($user != null){
                $state = $this->userStatesRepository->findByCode('ACTIVE');
                $this->userRepository->completeAccount($user->getUserId(),$state->getStateId(),$encrypt);
                return [
                    'result'=>true
                ];
            }
            throw new BadRequestException("No hemo logrado validar el usuario");
        }catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function sendRecoveryEmail(string $email): array{
        try {
            $user = $this->userRepository->findByEmail($email);
            if($user != null){
                $securityCode= Str::random(64);
                $this->userRepository->updateSecurity($user->getUserId(),$securityCode, 'RECOVERY');
                $identity = base64_encode($user->getUserId());
                $templateData = collect([
                    '{{UserName}}'=>$user->getName(),
                    '{{Identity}}'=>$identity,
                    '{{RecoveryCode}}'=>$securityCode,
                    '{{FrontUrl}}'=>config('app.front_url').'/recovery-password'
                ]); 
                $this->emailService->sendEmail("RECOVERY_ACC", $user->getEmail(), $templateData);
                return [
                    'result'=>true
                ];
            }
            throw new BadRequestException("No hemos logrado encontrar el usuario con el correo proporcionado");
        }catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function recoveryPassword(string $identity, string $code, string $password): array{
        try {
            $userIdentity =  base64_decode($identity);
            $encrypt =  Hash::make($password);
            $userAccount = $this->userRepository->getUserData($userIdentity, $code, 'RECOVERY');
            if($userAccount != null){
                $currentDate = Carbon::now('utc');
                $codeExpirationDate = Carbon::parse($userAccount->verify_date, 'utc')->addMinutes(30);
                if($currentDate->greaterThan($codeExpirationDate)){
                    throw new BadRequestException("El código de recuperación ha expirado, por favor solicite uno nuevo");
                }
                
                $this->userRepository->updatePassword($userAccount->user_id,$encrypt);
                return [
                    'result'=>true
                ];
            }
            throw new BadRequestException("No hemo logrado validar el usuario");
        }catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
                dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}