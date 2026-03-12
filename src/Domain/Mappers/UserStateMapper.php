<?php

namespace UesPlay\Domain\Mappers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


use UesPlay\Domain\Entities\UserState;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;

class UserStateMapper {
    
    public static function fromRawToEntity($raw): UserState {
        if($raw === null){
            throw new NotFoundException('El estado del usuario no existe');
        }

        $entity = new UserState();
        $entity->setStateId($raw->state_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);        
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(UserStateMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request): UserState{
        
        $entity = new UserState();
        $validate = Validator::make(
            $request->all(), 
            [
                'stateId'=>'uuid|required',
                'code'=>'required|string',
                'name'=>'nullable|string'
            ],
            [
                'stateId.uuid'=>'Error al transformar el id del estado',
                'stateId.required'=>'No se proporciono id del estado',
                'name.required'=>'El nombre es requerido',
                'code.required'=>'El nombre es requerido'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }

        $entity->setStateId($request->input('stateId'));
        $entity->setName($request->input('name'));
        $entity->setCode($request->input('code'));
        return $entity;
    }
}
