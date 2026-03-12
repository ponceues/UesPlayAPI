<?php
namespace UesPlay\Domain\Mappers;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\MediaGenre;


class MediaGenreMapper
{
    public static function fromRawToEntity($raw): MediaGenre
    {
        if ($raw === null) {
            throw new NotFoundException('La entidad MediaGenre no existe');
        }
        $entity = new MediaGenre();
        $entity->setGenreId($raw->genre_id);
        $entity->setMediaTypeId($raw->type_id);
        $entity->setName($raw->name);
        $entity->setEnabled($raw->enabled);
        $entity->setDescription($raw->description);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));
        return $entity;
    }

    public static function fromRawToCollection($raw): Collection
    {
        $list = collect();
        foreach ($raw as $item) {
            $list->push(MediaGenreMapper::fromRawToEntity($item));
        }
        return $list;
    }

    public static function fromRequestToEntity(Request $request, bool $isUpdate): MediaGenre
    {
        $validate = Validator::make(
            $request->all(),
            [
                'genreId' => [
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'mediaTypeId' => 'required|uuid',
                'name' => 'required|string',
                'enabled' => 'required|boolean',
                'description' => 'required|string',
            ],
            [
                'genreId.uuid' => 'El campo genreId debe ser un GUID',
                'mediaId.uuid' => 'El campo mediaId debe ser un GUID',
            ]
        );
        if ($validate->fails()) {
            throw new BadRequestException($validate->errors()->first());
        }
        $entity = new MediaGenre();
        if ($isUpdate) {
            $entity->setGenreId($request->input('genreId'));
        }
        $entity->setMediaTypeId($request->input('mediaTypeId'));
        $entity->setName($request->input('name'));
        $entity->setEnabled($request->input('enabled'));
        $entity->setDescription($request->input('description'));
        $entity->setCreatedAt(new DateTime());
        $entity->setUpdatedAt(new DateTime());
        return $entity;
    }
}