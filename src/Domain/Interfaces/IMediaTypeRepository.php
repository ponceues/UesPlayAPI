<?php
namespace UesPlay\Domain\Interfaces;

use UesPlay\Domain\Entities\MediaType;
use UesPlay\Domain\Helpers\Filter;
use Illuminate\Support\Collection;

interface IMediaTypeRepository
{
    function count(Filter $filter):int;
    function search(Filter $filter):Collection;
    function find(string $licenceId):MediaType;
    function findByName(string $name, ?string $licenceId ):?MediaType;
    function insert(MediaType $licence):MediaType;
    function update(MediaType $licence):MediaType;
    function delete(string $licenceId):bool;
}

