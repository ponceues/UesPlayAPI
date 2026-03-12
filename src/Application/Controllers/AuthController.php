<?php

namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Services\AuthService;
use UesPlay\Domain\Services\EmailService;

class AuthController extends Controller {
    private readonly AuthService $authService;
    private readonly EmailService $emailService;
    
    public function __construct(AuthService $authService, EmailService $emailService) {
        $this->authService = $authService;
        $this->emailService = $emailService;
    }

    public function login(Request $request): JsonResponse{
        
        $validationResult = Validator::make(
            $request->all(), 
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]
        );
        
        if($validationResult->invalid()){
          throw new   BadRequestException($validationResult->errors()->first());
        }
        
        $credentials = $request->only('email', 'password');
        $res = $this->authService->validateUsersAttempt($credentials);
        
        return response()->json($res);
    }
    
    public function getUsersSettings():JsonResponse{
        $res= $this->authService->getUserInformation();
        return response()->json($res);
    }
    
    public function verifyAccount(Request $request): JsonResponse {
        $validationResult = Validator::make(
            $request->all(), 
            [
                'identity' => 'required|string',
                'code'=>'required|string',
                'password'=>'required|string|confirmed'
                
            ]
        );
        
        $identity = $request->string('identity');
        $code = $request->string('code');
        $password = $request->string('password');

        if($validationResult->invalid()){
          throw new   BadRequestException($validationResult->errors()->first());
        }
        
        $res = $this->authService->verifyAccount($identity, $code, $password);
        return response()->json($res);
    }
    
    public function sendRecoveryEmail(Request $request): JsonResponse {
        $validationResult = Validator::make(
            $request->all(),
                [
                    'email'=>'required|string|email'
                    
                ],
                [
                    'email.required'=>'El campo email es obligatorio',
                    'email.email'=>'El formato del email es inválido'
                ]
            );
        
        $email = $request->string('email'); 
        if($validationResult->invalid()){
            throw new   BadRequestException($validationResult->errors()->first());
        }
        
        $res = $this->authService->sendRecoveryEmail($email);
        return response()->json($res);
    }
    
    public function recoveryAccount(Request $request): JsonResponse {
        $validationResult = Validator::make(
            $request->all(),
                [
                    'identity' => 'required|string',
                    'code'=>'required|string',
                    'password'=>'required|string|confirmed'
                ]
            );
        
        $identity = $request->string('identity');
        $code = $request->string('code');
        $password = $request->string('password');
        
        if($validationResult->invalid()){
            throw new   BadRequestException($validationResult->errors()->first());
        }
        
        $res = $this->authService->recoveryPassword($identity, $code, $password);
        return response()->json($res);
    }
    
}
