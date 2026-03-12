<?php

namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use UesPlay\Domain\Entities\User;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;

class UserMapper {
    public static function fromRawToEntity($raw): User {
        if($raw === null){
            throw new NotFoundException('La entidad no existe');
        }

        $entity = new User();
        $entity->setUserId($raw->user_id);
        $entity->setStateId($raw->state_id);
        $entity->setName($raw->name);
        $entity->setEmail($raw->email);
        $entity->setRolId($raw->rol_id);
        $entity->setEmailVerifiedAt($raw->email_verified_at === null ? null:new DateTime($raw->email_verified_at));
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(UserMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):User{
        
        $entity = new User();
        $validate = Validator::make(
            $request->all(),
            [
                'userId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name'=>'required|string',
                'email'=>'nullable|string',
                'password'=>'nullable|string|min:6',
                'stateId'=>[
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'rolId'=>'required|uuid',
                'areasCodes'=>'nullable|array',
                'areasCodes.*'=>'uuid',
                
            ],
            [
                'userId.uuid'=>'Error en la transaformacion del userId',
                'userId.required'=>'No se proporciono id del Rol',
                'name.required'=>'El nombre es requerido',
                'stateId.uuid'=>'El campo stateId es invalido',
                'stateId.required'=>'El campo stateId es requerido',
                'password.min'=>'La contraseña es muy corta',
                'rolId.required'=>'Se debe proporcionar un rol',
                'rolId.uuid'=>'El formato del campo RolId es invalido',
                'areasCodes.array'=>'El campo areasId debe ser una lista',
                'areasCodes.*.uuid'=>'Todos los elementos de areasId deben ser UUIDs válidos'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }

        $entity->setUserId($request->has('userId') ? $request->input('userId'): '');
        if($isUpdate){  
           $entity->setStateId($request->string('stateId'));            
        }
        $entity->setRolId($request->string('rolId'));
        $entity->setName($request->input('name'));
        $entity->setEmail($request->input('email'));
        $entity->setAreasCodes($request->array('areasCodes',[]));

        return $entity;
    }
}
