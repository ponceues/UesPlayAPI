<?php

namespace UesPlay\Domain\Mappers;


use UesPlay\Domain\Entities\ResourceState;
use UesPlay\Domain\Exceptions\NotFoundException;
use Illuminate\Support\Collection;
use DateTime;

class ResourceStateMapper {
     public static function fromRawToEntity($raw): ResourceState {
         
        if($raw === null){
            throw new NotFoundException('La entidad no existe');
        }

        $entity = new ResourceState();
        $entity->setStateId($raw->state_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);
        $entity->setCreatedAt(new DateTime($raw->created_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(ResourceStateMapper::fromRawToEntity($item));
        }
        return $list;
    }
}
