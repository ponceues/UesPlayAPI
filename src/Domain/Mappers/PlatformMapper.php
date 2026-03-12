<?php

namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Uesplay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Platform;


class PlatformMapper {
     public static function fromRawToEntity($raw): Platform {
        if($raw === null){
            throw new NotFoundException('La entidad buscada no existe.');
        }

        $entity = new Platform();
        $entity->setPlatformId($raw->platform_id);
        $entity->setName($raw->name);
        $entity->setDescription($raw->description);
        $entity->setIcon($raw->icon);
        $entity->setAvailable($raw->available);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(PlatformMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Platform{
        $entity = new Platform();
        $validate = Validator::make(
            $request->all(), 
            [
                'platformId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name'=>'required|string',
                'available'=>'required|boolean',
                'description'=>'string|nullable',
                'icon'=>'string|required'
            ],
            [
                'platformId.uuid'=>'El tipo de datos del campo {Id} debe ser un GUID',
                'platformId.required'=>'El campo Id es requerido',
                'name.required'=>'El nombre es requerido',
                'available.required'=>'El campo activo es requerido',
                'available.boolean'=>'El campo activo debe ser un booleano',
                'icon.required'=>'El icono es requerido'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setPlatformId($request->string('platformId'));
        }
        $entity->setName($request->string('name'));
        $entity->setIcon($request->string('icon'));
        $entity->setAvailable($request->boolean('available'));
        $entity->setDescription($request->string('description'));
        return $entity;
    }
}
