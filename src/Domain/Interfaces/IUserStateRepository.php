<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\UserState;

interface IUserStateRepository {
    function fetch(Filter $filter):Collection;
    function count(Filter $filter):int;
    function findByCode(string $code):UserState;
    function findById(string $stateId):UserState;
}
