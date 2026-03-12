<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\ResourceState;

interface IResourceStateRepository {
    function fetch(Filter $filter):Collection;
    function findByCode(string $stateId):ResourceState;
    function findById(string $stateId):ResourceState;
}
