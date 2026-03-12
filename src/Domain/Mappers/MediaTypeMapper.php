<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use UesPlay\Domain\Entities\MediaType;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;

class MediaTypeMapper {
    public static function fromRawToEntity($raw): MediaType {
        if ($raw === null) {
            throw new NotFoundException('La entidad {MediaType} no existe');
        }
        $entity = new MediaType();
        $entity->setTypeId($raw->type_id);
        $entity->setName($raw->name);
        $entity->setDescription($raw->description);
        $entity->setEnabled($raw->enabled);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));
        return $entity;
    }

    public static function fromRawToCollection($raw): Collection {
        $list = collect();
        foreach ($raw as $item) {
            $list->push(MediaTypeMapper::fromRawToEntity($item));
        }
        return $list;
    }

    public static function fromRequestToEntity(Request $request, bool $isUpdate): MediaType {
        $validate = Validator::make(
            $request->all(),
            [
                'typeId' => [
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name' => 'required|string',
                'description' => 'string|required',
                'enabled' => 'required|boolean',
            ],
            [
                'typeId.uuid' => 'El tipo de datos del campo {typeId} debe ser un GUID',
                'typeId.required' => 'El campo typeId es requerido',
                'name.required' => 'El nombre es requerido',
                'enabled.required' => 'El campo enabled es requerido',
                'enabled.boolean' => 'El campo enabled debe ser un booleano',
                'description.required' => 'La descripción es requerida',
                
            ]
        );
        if ($validate->fails()) {
            throw new BadRequestException($validate->errors()->first());
        }
        $entity = new MediaType();
        if ($isUpdate) {
            $entity->setTypeId($request->input('typeId'));
        }
        $entity->setName($request->input('name'));
        $entity->setDescription($request->input('description'));
        $entity->setEnabled($request->boolean('enabled'));
        return $entity;
    }
}