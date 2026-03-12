<?php

namespace UesPlay\Domain\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use DateTime;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Entities\Resource;

class ResourceMapper {
    
    public static function fromRawToEntity($raw): Resource {
        if($raw === null){
            throw new NotFoundException('El Tipo de recurso no existe');
        }

        $entity = new Resource();
        $entity->setResourceId($raw->resource_id);
        $entity->setUserId($raw->user_id);
        $entity->setTitle($raw->title);
        $entity->setDescription($raw->description);
        $entity->setStateId($raw->state_id);
        $entity->setTypeId($raw->type_id);
        $entity->setAreaId($raw->area_id);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));
        $entity->setDownloads($raw->downloads);
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(ResourceMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Resource{
        
        $entity = new Resource();
        $validate = Validator::make(
            $request->all(), 
            [
                'resourceId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'title'=>'required|string',
                'description'=>'required|string|min:10|max:500',
                'stateId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'typeId'=>'required|uuid',
                'areaId'=>'nullable|uuid',
                
            ],
            [
                'resourceId.uuid'=>'Error en el formato Id',
                'resourceId.required'=>'El Id del recurso es requerido',
                'title.required'=>'El titulo es requerido',
                'stateId.uuid'=>'El formato del id del estado es requerido',
                'typeId.uuid'=>'El formato del tipo de recurso es requerido',
                'areaId.uuid'=>'El formato id del area es incorrecto'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setResourceId($request->string('resourceId'));
            $entity->setStateId($request->string('stateId'));
        }
        $entity->setTitle($request->string('title'));
        $entity->setDescription($request->string('description'));
        $entity->setTypeId($request->string('typeId'));
        $entity->setAreaId($request->has('areaId') ? $request->string('areaId'):null);
        
        return $entity;
    }
}
