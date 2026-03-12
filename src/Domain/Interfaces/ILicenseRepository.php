<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\License;


interface ILicenseRepository
{
    function count(Filter $filter):int;
    function search(Filter $filter):Collection;
    function find(string $licenceId):License;
    function findByName(string $name, ?string $licenceId ):?License;
    function insert(License $licence):License;
    function update(License $licence):License;
    function delete(string $licenceId):bool;
}

