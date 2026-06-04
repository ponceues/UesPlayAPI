<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IPlatformRepository;
use UesPlay\Domain\Entities\Platform;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\PlatformMapper;


class PlatformRepository implements IPlatformRepository {

    private readonly string $table;
    
    public function __construct() {
        $this->table = 'platforms';
    }

    
    public function countByFilter(Filter $filter): int {
        $count =  DB::table($this->table)->count();
       
        return $count;
    }

    public function fetchByFilter(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        
        $raw = $query->orderBy('name','desc') 
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return PlatformMapper::fromRawToCollection($raw);
    }

    public function findById(string $id): Platform {
        $raw = DB::table($this->table)
                ->where('platform_id',$id)
                ->first();
        return PlatformMapper::fromRawToEntity($raw);
    }

    public function count(Filter $filter): int {
        $query =  DB::table($this->table)
                ->where('deleted',false);
        
        if($filter->getAvailable() !== null ){
            $query = $query->where('available',$filter->getAvailable());
        }
        
        if($filter->getText() !== null){
            $query = $query->whereAny([
                'name'
            ],'like','%'.$filter->getText().'%');
        }
        
        return $query->count();
    }

    public function countForUpdate(string $platformId, string $name): int {
         $count  = DB::table($this->table)
                ->where('name', $name)
                ->where('platform_id', '<>', $platformId)
                ->count();
        return $count;
    }

    public function create(Platform $entity): Platform {
        DB::table($this->table)
            ->insert([
                'platform_id'=>$entity->getPlatformId(),
                'name'=>$entity->getName(),
                'description'=>$entity->getDescription(),
                'available'=>$entity->getAvailable(),
                'icon'=>$entity->getIcon(),
                'deleted'=>false,
                'created_at'=>$entity->getCreatedAt(),
                'updated_at'=>$entity->getUpdatedAt()
            ]);
        return $this->findById($entity->getPlatformId());
    }

    public function delete(string $platformId): bool {
        DB::table($this->table)
            ->where('platform_id',$platformId)
            ->update([
                'deleted'=>true
            ]);
        return true;
    }

    public function fetch(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        if($filter->getAvailable() !== null ){
            $query= $query->where('available',$filter->getAvailable());
        }
        
        if($filter->getText() !== null){
            $query = $query->whereAny([
                'name'
            ],'like','%'.$filter->getText().'%');
        }
        
        $raw = $query->orderBy('name','desc') 
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        
        return PlatformMapper::fromRawToCollection($raw);
        
    }

    public function find(string $platformId): Platform {
        $raw = DB::table($this->table)
                ->where('platform_id', $platformId)
                ->where('deleted', true)
                ->first();
        return PlatformMapper::fromRawToEntity($raw);
    }

    public function findByName(string $name): ?Platform {
        $raw = DB::table($this->table)
                ->where('name', $name)
                ->first();
        if($raw === null){
            return null;
        }
        return PlatformMapper::fromRawToEntity($raw);
    }

    public function update(Platform $entity): Platform {
        DB::table($this->table)
            ->where('platform_id', $entity->getPlatformId())
            ->update([
                'name'=>$entity->getName(),
                'description'=>$entity->getDescription(),
                'available'=>$entity->getAvailable(),
                'icon'=>$entity->getIcon(),
                'updated_at'=>$entity->getUpdatedAt()
            ]);
        return $this->findById($entity->getPlatformId());
    }

    public function getSummary(): array {
        $totalNotDeleted = DB::table($this->table)
            ->where('deleted', false)
            ->count();
        
        $totalActive = DB::table($this->table)
            ->where('deleted', false)
            ->where('available', true)
            ->count();
        
        return [
            'total' => $totalNotDeleted,
            'active' => $totalActive
        ];
    }
}
