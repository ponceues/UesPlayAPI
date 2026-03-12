<?php
namespace UesPlay\Domain\Interfaces;
use UesPlay\Domain\Entities\ResourceFile;
use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;

interface IResourceFileRepository
{
    function fetch(Filter $filter, string $resourceId): Collection;
    function count(Filter $filter, string $resourceId): int;
    function create(ResourceFile $resourceFile): ResourceFile;
    function delete(string $resourceFileId): bool;
    function findByName(string $name, string $resourceId): ?ResourceFile;
    function find(string $resourceFileId): ?ResourceFile;
}

