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
                        throw new BadRequestException("Credenciales incorrectas. Intenta nuevamente.");
                    }
                    throw new BadRequestException("Credenciales incorrectas. Intenta nuevamente.");
                }
                throw new BadRequestException("Credenciales incorrectas. Intenta nuevamente.");
            }
            
            // Generar refresh token con el TTL configurado en .env
            $refreshTTL = (int) env('JWT_REFRESH_TTL', 360); // 6 horas por defecto
            $refreshToken = auth()->claims(['type' => 'refresh'])->setTTL($refreshTTL)->tokenById(auth()->user()->getAuthIdentifier());
            
            return [
                'username'=>auth()->user()->name,  
                'token'=>$token,
                'refreshToken'=>$refreshToken
            ];
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function refreshJwtToken(string $refreshToken): array{
        try{
            // Establecer el refresh token para validarlo
            auth()->setToken($refreshToken);
            
            // Verificar que el token sea válido
            $payload = auth()->getPayload();
            
            // Verificar que sea un refresh token
            if (!$payload->get('type') || $payload->get('type') !== 'refresh') {
                throw new BadRequestException("Token inválido. Se requiere un refresh token.");
            }
            
            // Obtener el ID del usuario desde el payload
            $userId = $payload->get('sub');
            
            // Verificar que el usuario existe y sigue activo
            $user = $this->userRepository->findById($userId);
            
            $state = $this->userStatesRepository->findById($user->getStateId());
            if (!$state || $state->getCode() !== 'ACTIVE') {
                throw new BadRequestException("Usuario no activo.");
            }
            
            // Generar un nuevo token de acceso con el TTL estándar
            $newToken = auth()->claims([])->tokenById($userId);
            
            return [
                'username' => $user->getName(),
                'token' => $newToken
            ];
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado al refrescar el token");
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
                    '{{FrontUrl}}'=>config('app.front_url').'/auth/recovery-password'
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

    public function resetPassword(string $identity, string $code, string $password): array{
        try {
            $userIdentity = base64_decode($identity);
            $encrypt = Hash::make($password);

            $user = $this->userRepository->verfifyAccountStep($userIdentity, $code,'RECOVERY');

            if ($user === null) {
                throw new BadRequestException("El código de verificación es inválido o ha expirado.");
            }

            $state = $this->userStatesRepository->findById($user->getStateId());
            if (!$state || $state->getCode() !== 'ACTIVE') {
                throw new BadRequestException("El usuario no se encuentra activo.");
            }

            $this->userRepository->updatePassword($user->getUserId(), $encrypt);

            return [
                'result' => true
            ];
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}