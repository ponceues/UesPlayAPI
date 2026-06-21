<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Entities\ResourceFile;

class FileMapper
{
    public static function fromRawToEntity($raw): ResourceFile {
        if($raw === null){
            throw new NotFoundException('La entidad {Area} no existe');
        }
        $entity = new ResourceFile();
        $entity->setFileId($raw->file_id);
        $entity->setResourceId($raw->resource_id);
        $entity->setName($raw->name);
        $entity->setExtension($raw->ext);
        $entity->setPath($raw->path);
        $entity->setOption($raw->option);
        $entity->setType($raw->type);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(FileMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request):ResourceFile {
        $entity = new ResourceFile();
        $valOption = Validator::make(
            $request->all(),
            [
                'option'=>'required|string|in:avatar,banner,media',
                'type'=>'required|in:image,video',
                'file'=>'file|required',
            ],
            [
                'option.in'=>'El valor del campo opción no es válido',
                'option.required'=>'El campo opción es obligatorio',
                'type.in'=>'El valor del campo tipo no es válido.',
                'type.required'=>'El campo tipo es obligatorio',
                'file.required'=>'El campo archivo es obligatorio',
                'file.file'=>'El campo archivo debe ser un archivo válido'
            ]
        );

        if($valOption->fails()){
            throw new BadRequestException($valOption->errors()->first());
        }
        
        $entity->setOption($request->string('option'));
        $entity->setType($request->string('type'));
        
        switch($entity->getOption()){
            case 'avatar':
                $validate = Validator::make(
                    $request->all(),
                    [
                        'file'=>'required|file|mimes:jpg,jpeg,png|max:5000|dimensions:max_width=900,max_height=900',
                    ]
                );
                $file = $request->file('file');
                $width = getimagesize($file)[0];
                $height = getimagesize($file)[1];
                if($width !== $height ){
                    throw new BadRequestException('El avatar debe ser una imagen cuadrada');
                }
                if($validate->fails()){
                    throw new BadRequestException($validate->errors()->first());
                }
            break;
            case 'banner':
            case 'media':
                if($entity->getType() === 'image'){
                    $validate = Validator::make(
                        $request->all(),
                            [
                                'file'=>'file|mimes:jpg,jpeg,png|max:10000|dimensions:max_width=1920,max_height=1080',
                            ]
                        );
                    $file = $request->file('file');
                    $width = getimagesize($file)[0];
                    $height = getimagesize($file)[1];
                    
                    if($validate->fails()){
                        throw new BadRequestException($validate->errors()->first());
                    }
                    
                    if($width/2 !== $height && ($width * 3) === ($height * 2)){
                        throw new BadRequestException('la imagen debe tener una relación de aspecto 2:1 o 2:3');
                    }
                    
                }else if($entity->getType() === 'video'){
                    $validate = Validator::make(
                        $request->all(),
                            [
                                'file'=>'required|file|mimes:mp4,mov,avi,mkv',
                            ]
                    );
                    if($validate->fails()){
                        throw new BadRequestException($validate->errors()->first());
                    }
                }
            break;
        }
        return $entity;
    }
    
}