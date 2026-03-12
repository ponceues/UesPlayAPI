<?php

namespace UesPlay\Domain\Mappers;

use DateTime;
use UesPlay\Domain\Entities\EmailTemplate;
use UesPlay\Domain\Exceptions\NotFoundException;

class EmailTemplateMapper {
    
    public static function fromRawToEntity($raw): EmailTemplate {
        if($raw === null){
            throw new NotFoundException('La entidad {EmailTemplate} no existe');
        }

        $entity = new EmailTemplate();
        $entity->setCode($raw->code);
        $entity->setContent($raw->content);
        $entity->setOrigin($raw->origin);
        $entity->setSubject($raw->subject);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        
        return $entity;
    }
}
