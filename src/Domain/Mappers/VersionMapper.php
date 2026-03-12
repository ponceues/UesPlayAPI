<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Version;
use UesPlay\Domain\Entities\Language;
use UesPlay\Domain\Entities\Platform;
use UesPlay\Domain\Entities\Device;

class VersionMapper
{
    public static function fromRawToEntity($raw): Version{ 
        if($raw === null){
            throw new NotFoundException('No se encontro la version.');
        }
        $entity = new Version();
        $entity->setVersionId($raw->version_id);
        $entity->setResourceId($raw->resource_id);
        $entity->setFileName($raw->file_name);
        $entity->setDescription($raw->description);
        $entity->setVersion($raw->version);
        $entity->setLicenseId($raw->license_id);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(VersionMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Version{
        
        $entity = new Version();
        
        // Deserializar langs, platforms y devices si vienen como string JSON
        foreach (['langs', 'platforms', 'devices'] as $param) {
            $raw = $request->input($param);
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (!is_array($decoded)) {
                    throw new BadRequestException("El parámetro $param debe ser un array serializado en formato JSON.");
                }
                $request->merge([$param => $decoded]);
            }
        }
        
        $validate = Validator::make(
            $request->all(),
            [
                'versionId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'resourceId'=>'required|uuid',
                'description'=>'string',
                'version'=>'string|required',
                'langs' => ['array', 'required'],
                'langs.*' => ['string', 'uuid'],
                'platforms' => ['array', 'required'],
                'platforms.*' => ['string', 'uuid'],
                'devices' => ['array', 'required'],
                'devices.*' => ['string', 'uuid'],
                'source'=>'file|required|mimes:zip,rar',
                'licenseId'=>'uuid|required'
            ],
            [
                'resourceId.required'=>'El id del recurso es requerido.',
                'versionId.required'=>'El id de la version es requerido.',
                'versionId.uuid'=>'El id de la version debe ser un UUID valido.',
                'description.string'=>'La descripcion debe ser una cadena de texto.',
                'version.string'=>'La version debe ser una cadena de texto.',
                'version.required'=>'La version es requerida.',
                'langs.array'=>'Los idiomas deben ser un arreglo.',
                'langs.required'=>'Los idiomas son requeridos.',
                'langs.*.string'=>'Cada idioma debe ser una cadena de texto.',
                'langs.*.uuid'=>'Cada idioma debe ser un UUID valido.',
                'platforms.array'=>'Las plataformas deben ser un arreglo.',
                'platforms.required'=>'Las plataformas son requeridas.',
                'platforms.*.string'=>'Cada plataforma debe ser una cadena de texto.',
                'platforms.*.uuid'=>'Cada plataforma debe ser un UUID valido.',
                'devices.array'=>'Los dispositivos deben ser un arreglo.',
                'devices.required'=>'Los dispositivos son requeridos.',
                'devices.*.string'=>'Cada dispositivo debe ser una cadena de texto.',
                'devices.*.uuid'=>'Cada dispositivo debe ser un UUID valido.',
                'source.file'=>'El archivo fuente debe ser un archivo.',
                'source.required'=>'El archivo fuente es requerido.',
                'licenseId.uuid'=>'El id de la licencia debe ser un UUID valido.',
                'licenseId.required'=>'El id de la licencia es requerido.'
                
            ]
            );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setVersionId($request->input('versionId'));
        }
        $entity->setResourceId($request->input('resourceId'));
        $entity->setVersion($request->input('version'));
        $entity->setDescription($request->string('description'));

        // Obtener y mapear los idiomas
        $languages = collect();
        foreach ($request->input('langs', []) as $langId) {
            $language = new Language();
            $language->setLanguageId($langId);
            $languages->push($language);
        }
        $entity->setLanguages($languages);
        $entity->setLicenseId($request->string('licenseId'));
        // Mapear devices
        $devices = collect();
        foreach ($request->input('devices', []) as $deviceId) {
            $device = new Device();
            $device->setDeviceId($deviceId);
            $devices->push($device);
        }
        $entity->setDevices($devices);
        // Mapear platforms
        $platforms = collect();
        foreach ($request->input('platforms', []) as $platformId) {
            $platform = new Platform();
            $platform->setPlatformId($platformId);
            $platforms->push($platform);
        }
        $entity->setPlatforms($platforms);
        return $entity;
    }
    

}