<?php

namespace UesPlay\Domain\Interfaces;

use DateTime;
use Illuminate\Support\Collection;

use UesPlay\Domain\Helpers\Filter;


interface IPermissionRepository {
    function addToRol(string $rolId, $permissionId, DateTime $createdAt):bool;
    function fetchByFilter(Filter $filter):Collection;
    function fetchByRol(string $rolId):Collection;
    function removeFromRol(string $rolId, string $permissionId):bool;
}
