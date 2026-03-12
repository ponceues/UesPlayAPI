<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\ResourceType;
use UesPlay\Domain\Helpers\Filter;

interface IResourceTypeRepository {
    function countByFilter( Filter $filter):int;
    function countForUpdate(ResourceType $entity):int;
    function countForCreate(ResourceType $entity):int;
    function delete(string $id):bool;
    function insert(ResourceType $entity):ResourceType;
    function fetchByFilter(Filter $filter):Collection;
    function findById(string $id):ResourceType;
    function update(ResourceType $entity):ResourceType;
}
