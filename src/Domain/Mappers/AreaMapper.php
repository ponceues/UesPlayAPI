<?php

namespace UesPlay\Domain\Mappers;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;

use UesPlay\Domain\Entities\Area;

class AreaMapper {
        
    public static function fromRawToEntity($raw): Area {
        if($raw === null){
            throw new NotFoundException('La entidad {Area} no existe');
        }
        $entity = new Area();
        $entity->setAreaId($raw->area_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);
        $entity->setActive($raw->active);
        $entity->setDescription($raw->description);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(AreaMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Area{
        
        $entity = new Area();
        $validate = Validator::make(
            $request->all(), 
            [
                'areaId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'code'=>'required|string',
                'name'=>'required|string',
                'active'=>'required|boolean',
                'description'=>'string'
            ],
            [
                'areaId.uuid'=>'El tipo de datos del campo {Id} debe ser un GUID',
                'areaId.required'=>'El campo Id es requerido',
                'name.required'=>'El nombre es requerido',
                'code.required'=>'El campo codigo es requerido',
                'active.required'=>'El campo activo es requerido',
                'active.boolean'=>'El campo activo debe ser un booleano'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setAreaId($request->input('areaId'));
        }
        $entity->setName($request->input('name'));
        $entity->setCode($request->input('code'));
        $entity->setActive($request->boolean('active'));
        $entity->setDescription($request->string('description'));
        return $entity;
    }
}
