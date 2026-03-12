<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use UesPlay\Domain\Entities\License;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;

class LicenseMapper
{
    public static function fromRawToEntity($raw): License {
        if($raw === null){
            throw new NotFoundException('La entidad Licence no existe');
        }
        $entity = new License();
        $entity->setLicenseId($raw->license_id);
        $entity->setName($raw->name);
        $entity->setVersion($raw->version);
        $entity->setDescription($raw->description);
        $entity->setEnabled($raw->enabled);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));
        return $entity;
    }

    public static function fromRawToCollection($raw): Collection {
        $list = collect();
        foreach($raw as $item){
            $list->push(self::fromRawToEntity($item));
        }
        return $list;
    }

    public static function fromRequestToEntity(Request $request, bool $isUpdate): License {
        $validate = Validator::make(
            $request->all(),
            [
                'licenseId' => [
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'name' => ['required', 'string', 'max:255'],
                'version' => ['required', 'string', 'max:50'],
                'enabled' => ['required', 'boolean'],
                'description' => ['nullable', 'string', 'max:500'],
            ],
            [
                'licenseId.required' => 'El campo licenseId es obligatorio.',
                'licenseId.uuid' => 'El campo licenseId debe ser un UUID válido.',
                'name.required' => 'El campo name es obligatorio.',
                'name.string' => 'El campo name debe ser una cadena de texto.',
                'name.max' => 'El campo name no debe exceder los 255 caracteres.',
                'version.required' => 'El campo version es obligatorio.',
                'version.string' => 'El campo version debe ser una cadena de texto.',
                'version.max' => 'El campo version no debe exceder los 50 caracteres.',
                'description.string' => 'El campo description debe ser una cadena de texto.',
                'description.max' => 'El campo description no debe exceder los 500 caracteres.',
                'enabled.required' => 'El campo enabled es obligatorio.',
            ]
        );
        if ($validate->fails()) {
            throw new BadRequestException($validate->errors()->first());
        }
        $entity = new License();
        if($isUpdate) {
            $entity->setLicenseId($request->input('licenseId'));
        }
        $entity->setName($request->input('name'));
        $entity->setVersion($request->input('version'));
        $entity->setDescription($request->input('description'));
        $entity->setEnabled($request->boolean('enabled'));
        $entity->setCreatedAt(new DateTime());
        $entity->setUpdatedAt(new DateTime());
        return $entity;
    }
}