<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Entities\Author;
use UesPlay\Domain\Mappers\AuthorMapper;
use UesPlay\Domain\Interfaces\IAuthorRepository;


class AuthorRepository implements IAuthorRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'authors';
    }
    
    public function find(string $authorId): Author
    {
        $raw = DB::table($this->table)
            ->where('author_id', $authorId)
            ->first();
        
        return AuthorMapper::fromRawToEntity($raw);
    }
    
    public function update(Author $author): Author
    {
        DB::table($this->table)
            ->where('author_id', $author->getAuthorId())
            ->update([
                'firs_name' => $author->getFirstName(),
                'last_name' => $author->getLastName(),
                'email' => $author->getEmail(),
                'updated_at' => $author->getUpdatedAt()
            ]);
            return $this->find($author->getAuthorId());
    }

    public function delete(string $authorId): bool
    {
        return DB::table($this->table)
            ->where('author_id', $authorId)
            ->update([
                'deleted_at' => true
                ]);
        return true;
    }

    public function findByEmail(string $email): ?Author
    {
        $raw = DB::table($this->table)
            ->where('email', $email)
            ->first();
        
            if ($raw === null) {
                return null;
            }
        return AuthorMapper::fromRawToEntity($raw);
        
    }

    public function insert(Author $author): Author
    {
        DB::table($this->table)
            ->insert([
                'author_id' => $author->getAuthorId(),
                'first_name' => $author->getFirstName(),
                'last_name' => $author->getLastName(),
                'email' => $author->getEmail(),
                'created_at' => $author->getCreatedAt(),
                'updated_at' => $author->getUpdatedAt()
            ]);
        return $this->find($author->getAuthorId());
    }

    
}

