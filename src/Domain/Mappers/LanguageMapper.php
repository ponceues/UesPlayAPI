<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Language;

class LanguageMapper
{
    public static function fromRawToEntity($raw): Language {
        if($raw === null){
            throw new NotFoundException('El lenguage no existe');
        }
        $entity = new Language();
        $entity->setLanguageId($raw->language_id);
        $entity->setCode($raw->code);
        $entity->setName($raw->name);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        
        return $entity;
    }
    
    public static function fromRawToCollection($raw):Collection{
        $list = collect();
        foreach($raw as $item){
            $list->push(LanguageMapper::fromRawToEntity($item));
        }
        return $list;
    }
    

    
}

