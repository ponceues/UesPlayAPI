<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Entities\Author;

class AuthorMapper
{
    public static function fromRawToEntity($raw): Author {
        if($raw === null){
            throw new NotFoundException('La entidad {Area} no existe');
        }
        $entity = new Author();
        $entity->setAuthorId($raw->author_id);
        $entity->setFirstName($raw->first_name);
        $entity->setLastName($raw->last_name);
        $entity->setEmail($raw->email);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));
        
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(AuthorMapper::fromRawToEntity($item));
        }
        return $list;
    }
    
    public static function fromRequestToEntity(Request $request, bool $isUpdate):Author {
        
        $entity = new Author();
        $validate = Validator::make(
            $request->all(),
            [
                'authorId'=>[
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'firstName'=>'required|string',
                'lastName'=>'required|string',
                'email'=>'required|email|string',
            ],
            [
                'authorId.required'=>'El identificador del autor es obligatorio',
                'authorId.uuid'=>'El identificador del autor debe ser un UUID valido',
                'firstName.required'=>'El nombre del autor es obligatorio',
                'firstName.string'=>'El nombre del autor debe ser una cadena de texto',
                'lastName.required'=>'El apellido del autor es obligatorio',
                'lastName.string'=>'El apellido del autor debe ser una cadena de texto',
                'email.required'=>'El correo electronico del autor es obligatorio',
                'email.email'=>'El correo electronico del autor debe ser un correo valido',
                'email.string'=>'El correo electronico del autor debe ser una cadena de texto',
            ]
            );
        
        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }
        if($isUpdate){
            $entity->setAuthorId($request->input('authorId'));
        }
        $entity->setFirstName($request->input('firstName'));
        $entity->setLastName($request->input('lastName'));
        $entity->setEmail($request->input('email'));
        return $entity;
    }
}

