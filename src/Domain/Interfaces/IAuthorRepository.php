<?php

namespace UesPlay\Domain\Interfaces;

use UesPlay\Domain\Entities\Author;

interface IAuthorRepository {
    
    function find(string $authorId): Author;
    function insert(Author $author): Author;
    function update(Author $author): Author;
    function delete(string $authorId): bool;
    function findByEmail(string $email): ?Author;
}
