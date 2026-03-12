<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;

interface IResourceAuthorRepository
{
    function assignAuthorToResource(string $resourceId, string $authorId): bool;
    function removeAuthorFromResource(string $resourceId, string $authorId): bool;
    function getAuthorsByResource(string $resourceId): Collection;
    function countAuthorsByResource(string $resourceId): int;
    function countByAuthorAndResource(string $resourceId, string $authorId): int;
}

