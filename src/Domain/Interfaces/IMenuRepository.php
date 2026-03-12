<?php

namespace UesPlay\Domain\Interfaces;

use DateTime;
use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;

interface IMenuRepository {    
    function addMenuToRol(string $rolId, string $menuId,DateTime $timeUtc ):bool;
    function fetchMenus(Filter $filter):Collection;
    function fetchByRol(string $rolId):Collection;
    function removeMenuFromRol(string $rolId, string $menuId): bool;

}
