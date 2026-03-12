<?php

namespace UesPlay\Domain\Mappers;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Device;

class DeviceMapper {
    public static function fromRawToEntity($raw): Device {
        if($raw === null){
            throw new NotFoundException('La entidad buscada no existe.');
        }

        $entity = new Device();
        $entity->setDeviceId($raw->device_id);
        $entity->setName($raw->name);
        $entity->setDescription($raw->description);
        $entity->setActive($raw->active);
        $entity->setIcon($raw->icon);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->created_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(DeviceMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Device{
        $entity = new Device();
        $validate = Validator::make(
            $request->all(), 
            [
                'deviceId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name'=>'required|string',
                'active'=>'required|boolean',
                'description'=>'string|nullable',
                'icon'=>'string|required'
            ],
            [
                'deviceId.uuid'=>'El tipo de datos del campo {Id} debe ser un GUID',
                'device.required'=>'El campo Id es requerido',
                'name.required'=>'El nombre es requerido',
                'active.required'=>'El campo activo es requerido',
                'active.boolean'=>'El campo activo debe ser un booleano',
                'icon.required'=>'El icono es requerido'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setDeviceId($request->input('deviceId'));
        }
        $entity->setName($request->string('name'));
        $entity->setIcon($request->string('icon'));
        $entity->setActive($request->boolean('active'));
        $entity->setDescription($request->string('description'));
        return $entity;
        
    }
}
