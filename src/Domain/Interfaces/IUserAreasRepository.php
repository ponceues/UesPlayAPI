<?php

namespace UesPlay\Domain\Interfaces;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Entities\User;

interface IUserAreasRepository {
    function count(Area $area, User $user):int;
    function insert(Area $area, User $user):bool;
}
