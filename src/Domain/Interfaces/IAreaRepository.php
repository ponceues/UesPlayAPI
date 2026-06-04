<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;

use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Helpers\Filter;


interface IAreaRepository {
    function countByCode(string $code):int;
    function countByFilter(Filter $filter):int;
    function countForCreate(Area $area):int;
    function countForUpdate(Area $area):int;
    function delete(string $areaId):bool;
    function fetchByFilter(Filter $filter):Collection;
    function findByCode(string $code):Area;
    function findById(string $areaId):Area;
    function fetchByUser(string $userId):Collection;
    function fetchFullByUser(string $userId):Collection;
    function insert(Area $area):Area;
    function update(Area $area):Area;
    function countTotal():int;
    function countActive():int;
    
}
