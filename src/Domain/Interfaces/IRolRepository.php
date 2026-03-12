<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\Rol;
use UesPlay\Domain\Helpers\Filter;

interface IRolRepository {
    function countByFilter(Filter $filter):int;
    function countForUpdate(Filter $filter):int;
    function createRol(Rol $rol):Rol;
    function deleteRol(string $rolId):bool;
    function fetchRoles(Filter $filter): Collection;
    function findById(string $rolId):Rol;
    function updateRol(Rol $rol):Rol;
    
    
}
