<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\Version;
use UesPlay\Domain\Helpers\Filter;


interface IVersionRepository
{
    function fetchByResource(string $resourceId, Filter $filter):Collection;
    function countByResource(string $resourceId, Filter $filter):int;
    function find(string $versionId):Version;
    function update(Version $version):Version;
    function insert(Version $version):Version;
    function delete(Version $version):Version;
    function findLastByResource(string $resourceId):Version;
}

