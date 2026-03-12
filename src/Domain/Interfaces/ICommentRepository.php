<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\Comment;
use UesPlay\Domain\Helpers\Filter;

interface ICommentRepository
{
    function count(Filter $filter, string $resourceId):int;
    function search(Filter $filter, string $resourceId):Collection;
    function find(string $commentId):Comment;
    function insert(Comment $comment):Comment;
    function update(Comment $comment):Comment;
    function delete(string $commentId):bool;
}