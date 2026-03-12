<?php
namespace UesPlay\Infrastructure\Repositories;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IResourceFileRepository;
use UesPlay\Domain\Entities\ResourceFile;
use UesPlay\Domain\Mappers\FileMapper;

class ResourceFileRepository implements IResourceFileRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'resource_files';
    }
    
    public function fetchByResourceId(string $resourceId): Collection
    {
        $query = DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('deleted',false);
        
            $raw = $query->orderBy('created_at','asc')
            ->get();
            
            return FileMapper::fromRawToCollection($raw);
    }

    public function create(ResourceFile $file): ResourceFile
    {
        DB::table($this->table)
            ->insert([
                'file_id' => $file->getFileId(),
                'resource_id' => $file->getResourceId(),
                'name' => $file->getName(),
                'ext'=> $file->getExtension(),
                'deleted' => false,
                'path'  => $file->getPath(),
                'option' => $file->getOption(),
                'type' => $file->getType(),
                'created_at' => $file->getCreatedAt()
        ]);

            return $this->find($file->getFileId());
    }

    public function findByName(string $name, string $resourceId): ?ResourceFile
    {
        $raw = DB::table($this->table)
            ->where('name', $name)
            ->where('resource_id', $resourceId)
            ->first();
        if ($raw) {
            return FileMapper::fromRawToEntity($raw);
        }
        return null;
    }

    public function delete(string $resourceFileId): bool
    {
        DB::table($this->table)
            ->where('file_id', $resourceFileId)
            ->delete();
        return true;
    }

    public function find(string $resourceFileId): ?ResourceFile
    {
        $raw = DB::table($this->table)
            ->where('file_id', $resourceFileId)
            ->where('deleted', false)
            ->first();
        return FileMapper::fromRawToEntity($raw);
    }
    
    public function fetch(Filter $filter, string $resourceId): Collection
    {
        
        $query =  DB::table($this->table)
                        ->where('resource_id', $resourceId)
                        ->where('deleted',false);
        
        $raw = $query->orderBy('created_at','asc')
        ->offset($filter->getPage()*$filter->getPageSize())
        ->limit($filter->getPageSize())
        ->get();
        return FileMapper::fromRawToCollection($raw);
    }


    public function count(Filter $filter, string $resourceId): int
    {
        $query =  DB::table($this->table)
                        ->where('resource_id', $resourceId)
                        ->where('deleted',false);
        
       
        return $query->count();
    }

}

