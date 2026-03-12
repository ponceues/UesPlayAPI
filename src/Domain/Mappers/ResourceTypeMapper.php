<?php

namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Entities\ResourceType;

class ResourceTypeMapper {
    
    public static function fromRawToEntity($raw): ResourceType {
        if($raw === null){
            throw new NotFoundException('El Tipo de recurso no existe');
        }

        $entity = new ResourceType();
        $entity->setTypeId($raw->type_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);
        $entity->setActive($raw->active);
        $entity->setDeleted($raw->deleted);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(ResourceTypeMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):ResourceType{
        
        $entity = new ResourceType();
        $validate = Validator::make(
            $request->all(), 
            [
                'typeId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name'=>'required|string',
                'active'=>'required|boolean'
            ],
            [
                'typeId.uuid'=>'El tipo de datos del campo :Id debe ser un GUID',
                'name.required'=>'El nombre es requerido',
                'active.required'=>'El campo activo es requerido',
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setTypeId($request->input('typeId'));
        }
        $entity->setName($request->input('name'));
        $entity->setActive($request->boolean('active'));
        return $entity;
    }
}
