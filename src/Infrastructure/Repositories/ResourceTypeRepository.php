<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IResourceTypeRepository;
use UesPlay\Domain\Entities\ResourceType;
use UesPlay\Domain\Mappers\ResourceTypeMapper;
use UesPlay\Domain\Helpers\Filter;

class ResourceTypeRepository implements IResourceTypeRepository {
    
    private readonly string $table;
    
    public function __construct() {
        $this->table = 'resource_types';
    }

    public function countByFilter(Filter $filter): int {
        $query = DB::table($this->table)
                ->where('deleted',false);
        return $query->count();
    }
    
    public function countForCreate(ResourceType $entity): int {
         $count = DB::table($this->table)
                ->where('name',$entity->getName())
                ->count();
        return $count;
    }

    public function countForUpdate(ResourceType $entity): int {
        $count = DB::table($this->table)
                ->where('type_id','<>',$entity->getTypeId())
                ->where('name',$entity->getName())
                ->count();
        return $count;
    }

    public function delete(string $id): bool {
        DB::table($this->table)
            ->where('type_id',$id)
            ->update([
                'deleted'=>true
            ]);
        return true;
    }

    public function fetchByFilter(Filter $filter): Collection {
        $query = DB::table($this->table)
            ->where('deleted',false);
        if($filter->getName() != null){
            $query->where('name',$filter->getName());
        }
        
        if($filter->getActive() != null){
            $query->where('active',$filter->getActive());
        }
        
        $raw = $query->orderBy('created_at','desc') 
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return ResourceTypeMapper::fromRawToCollection($raw);
    }

    public function findById(string $id): ResourceType {
        $raw = DB::table($this->table)
            ->where('type_id',$id)
            ->first();
        return ResourceTypeMapper::fromRawToEntity($raw);
    }

    public function insert(ResourceType $entity): ResourceType {
        DB::table($this->table)
            ->insert([
                'type_id'=>$entity->getTypeId(),
                'name'=>$entity->getName(),
                'active'=>$entity->getActive(),
                'deleted'=>$entity->getDeleted(),
                'created_at'=>$entity->getCreatedAt(),
                'updated_at'=>$entity->getUpdatedAt()
            ]);
        return $this->findById($entity->getTypeId());
    }

    public function update(ResourceType $entity): ResourceType {
        DB::table($this->table)
            ->where('type_id',$entity->getTypeId())
            ->update([
                'name'=>$entity->getName(),
                'active'=>$entity->getActive(),
                'updated_at'=>$entity->getUpdatedAt()
            ]);
        return $this->findById($entity->getTypeId());
    }


}
