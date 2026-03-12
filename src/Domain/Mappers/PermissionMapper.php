<?php

namespace UesPlay\Domain\Mappers;

use DateTime;
use UesPlay\Domain\Entities\Permission;
use Illuminate\Support\Collection;
use UesPlay\Domain\Exceptions\NotFoundException;

class PermissionMapper {
    public static function fromRawToEntity($raw): Permission {
        if($raw === null){
            throw new NotFoundException('La entidad {Permission} no existe');
        }

        $entity = new Permission();
        $entity->setPermissionId($raw->permission_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);
        $entity->setMenuId($raw->menu_id);
        $entity->setCreatedAt(new DateTime($raw->created_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(PermissionMapper::fromRawToEntity($item));
        }
        return $list;
    }
}
