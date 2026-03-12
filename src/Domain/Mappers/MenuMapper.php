<?php

namespace UesPlay\Domain\Mappers;

use Illuminate\Support\Collection;
use DateTime;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Menu;


class MenuMapper {
    
    public static function fromRawToEntity($raw): Menu {
        if($raw === null){
            throw new NotFoundException('La entidad {Menu} no existe');
        }

        $entity = new Menu();
        $entity->setMenuId($raw->menu_id);
        $entity->setName($raw->name);
        $entity->setIcon($raw->icon);
        $entity->setRoute($raw->route);
        $entity->setParentId($raw->parent_id);
        $entity->setCreatedAt(new DateTime($raw->created_at));

        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(MenuMapper::fromRawToEntity($item));
        }
        return $list;
    }
}
