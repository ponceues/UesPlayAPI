<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\MediaGenre;

interface IMediaGenreRepository
{
    function count(Filter $filter):int;
    function search(Filter $filter):Collection;
    function find(string $mediaGenreId):MediaGenre;
    function findByName(string $name, ?string $mediaGenreId ):?MediaGenre;
    function insert(MediaGenre $mediaGenre):MediaGenre;
    function update(MediaGenre $mediaGenre):MediaGenre;
    function delete(string $mediaGenreId):bool;    
}

