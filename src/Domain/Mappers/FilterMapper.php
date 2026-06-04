<?php

namespace UesPlay\Domain\Mappers;

use UesPlay\Domain\Helpers\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use UesPlay\Domain\Exceptions\BadRequestException;

class FilterMapper {
    
    public static function fromRequestToEntity(Request $request):Filter{
        $validate = Validator::make(
            $request->all(),
            [   'page'=>'nullable|numeric|min:0',
                'pageSize'=>'nullable|numeric|min:1',
                'state'=>'nullable|string',
            ]
        );

        if ($validate->fails()) {
            throw new BadRequestException($validate->errors()->first());
        }

        $entity= new Filter();
        $entity->setPage($request->has('page')? (int)$request->input('page'):0);
        $entity->setPageSize($request->has('pageSize')? (int)$request->input('pageSize'):10);
        $entity->setAvailable($request->has('available')? $request->boolean('available'): null);
        $entity->setStateId($request->has('stateId')? $request->string('stateId'): null);
        $entity->setText($request->has('text')? $request->string('text'): null);
        $entity->setTypeId($request->has('typeId')? $request->string('typeId'): null);
        $entity->setPlatformId($request->has('platformId')? $request->string('platformId'): null);
        $entity->setDeviceId($request->has('deviceId')? $request->string('deviceId'): null);
        $entity->setStateId($request->has('stateId')? $request->string('stateId'): null);
        $entity->setAreaId($request->has('areaId')? $request->string('areaId'): null);
        $entity->setEnabled($request->has('enabled') ? $request->boolean('enabled'): null);
        return $entity;
    }
    
}
