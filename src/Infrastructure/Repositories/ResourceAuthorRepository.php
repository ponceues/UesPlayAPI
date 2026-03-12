<?php
namespace UesPlay\Infrastructure\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


use UesPlay\Domain\Interfaces\IResourceAuthorRepository;
use UesPlay\Domain\Mappers\AuthorMapper;

class ResourceAuthorRepository implements IResourceAuthorRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'resource_authors';
    }

    public function getAuthorsByResource(string $resourceId): Collection
    {
        $raw = DB::table($this->table)
            ->join('authors', 'resource_authors.author_id', '=', 'authors.author_id')
            ->where('resource_authors.resource_id', $resourceId)
            ->where('authors.deleted', false)
            ->select('authors.*')
            ->get();

        return AuthorMapper::fromRawToCollection($raw);
    }
    
    public function countAuthorsByResource(string $resourceId): int
    {
        $raw = DB::table($this->table)
            ->join('authors', 'resource_authors.author_id', '=', 'authors.author_id')
            ->where('resource_authors.resource_id', $resourceId)
            ->where('authors.deleted', false);
        
        return $raw->count();
    }

    public function assignAuthorToResource(string $resourceId, string $authorId): bool
    {
        DB::table($this->table)->insert([
            'resource_id' => $resourceId,
            'author_id' => $authorId,
            'created_at' => Carbon::now('utc'),
            'updated_at' => Carbon::now('utc')
        ]);
        return true;
    }

    public function removeAuthorFromResource(string $resourceId, string $authorId): bool
    {
        DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('author_id', $authorId)
            ->delete();
        return true;
    }

    public function countByAuthorAndResource(string $resourceId, string $authorId): int
    {
        $raw = DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('author_id', $authorId);
        
        return $raw->count();
    }


    
    
    
}

