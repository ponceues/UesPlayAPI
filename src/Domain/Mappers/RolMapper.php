<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use UesPlay\Domain\Entities\Rol;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
class RolMapper {
    
    public static function fromRawToEntity($raw): Rol {
        if($raw === null){
            throw new NotFoundException('La entidad {Rol} no existe');
        }

        $entity = new Rol();
        $entity->setRolId($raw->rol_id);
        $entity->setName($raw->name);
        $entity->setIsActive($raw->is_active);
        $entity->setIsDefault($raw->is_default);
        $entity->setIsDelete($raw->is_deleted);
        $entity->setDescription($raw->description);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdateAt(new DateTime($raw->updated_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(RolMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Rol{
        
        $entity = new Rol();
        $validate = Validator::make(
            $request->all(), 
            [
                'rolId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name'=>'required|string',
                'description'=>'nullable|string',
                'isActive'=>'required|boolean'
            ],
            [
                'rolId.uuid'=>'El tipo de datos del campo {rolId} no compatible',
                'rolId.required'=>'No se proporciono id del Rol',
                'name.required'=>'El nombre del rol es requerido',
                'isActive.required'=>'El campo {isActive} es requerido',
                'isActive.boolean'=>'El campo {isActive} es debe ser booleano'
            ]
        );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setRolId($request->input('rolId'));
        }
        $entity->setName($request->input('name'));
        $entity->setDescription($request->input('description'));
        $entity->setIsActive($request->boolean('isActive'));
        $entity->setIsDelete(false);
        return $entity;
    }
}
