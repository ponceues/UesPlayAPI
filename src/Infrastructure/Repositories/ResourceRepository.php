<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


use UesPlay\Domain\Interfaces\IResourceRepository;
use UesPlay\Domain\Entities\Resource;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\ResourceMapper;

class ResourceRepository implements IResourceRepository {

    private readonly string $table;

    public function __construct() {
        $this->table = 'resources';
    }


    public function countByFilter(Filter $filter): int {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        if($filter->getText() !== null){
             $query = $query->whereAny([
                'title'
            ],'like','%'.$filter->getText().'%');
        }
        
        if($filter->getStateId() !== null){
            $query = $query->where('state_id',$filter->getStateId());
        }
        
        if($filter->getAreaId() !== null){
            $query = $query->where('area_id',$filter->getAreaId());
        }
        
        
        return $query->count();
    }

    public function countForUpdate(Resource $resource): int {
        $raw = DB::table($this->table)
                ->where('title',$resource->getTitle())
                ->where('resource_id','<>',$resource->getResourceId())
                ->where('deleted',false);
        return $raw->count(); 
    }

    public function delete(Resource $resource): bool {
        DB::table($this->table)
            ->where('resource_id',$resource->getResourceId())
            ->update([
                'deleted'=>true
            ]);
        return true;
    }

    public function fetchByFilter(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        if($filter->getText() !== null){
             $query = $query->whereAny([
                'title'
            ],'like','%'.$filter->getText().'%');
        }
        
        if($filter->getStateId() !== null){
            $query = $query->where('state_id',$filter->getStateId());
        }
        
        if($filter->getAreaId() !== null){
            $query = $query->where('area_id',$filter->getAreaId());
        }
        
        if($filter->getTypeId() !== null){
            $query = $query->where('type_id',$filter->getTypeId());
            
        }
        
        
        $raw = $query->orderBy('created_at','desc')
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return ResourceMapper::fromRawToCollection($raw);
    }

    public function findByTitle(string $title): ?Resource {
        $raw = DB::table($this->table)
                ->where('title',$title)
                ->where('deleted',false)
                ->first();
        if($raw === null){
            return null;
        }
        
        return ResourceMapper::fromRawToEntity($raw); 
    }
    
    public function findOrFail(string $resourceId): Resource {
         $raw = DB::table($this->table)
                ->where('resource_id',$resourceId)
                ->where('deleted',false)
                ->first();
        return ResourceMapper::fromRawToEntity($raw);
    }

    public function insert(Resource $resource): Resource {
        DB::table($this->table)
            ->insert([
                'resource_id'=>$resource->getResourceId(),
                'user_id'=>$resource->getUserId(),
                'state_id'=>$resource->getStateId(),
                'title'=>$resource->getTitle(),
                'description'=>$resource->getDescription(),
                'deleted'=>false,
                'type_id'=>$resource->getTypeId(),
                'area_id'=>$resource->getAreaId(),
                'downloads'=>0,
                'created_at'=>$resource->getCreatedAt(),
                'updated_at'=>$resource->getUpdatedAt()
            ]);
        return $this->findOrFail($resource->getResourceId());
    }

    public function update(Resource $resource): Resource {
        DB::table($this->table)
            ->where('resource_id', $resource->getResourceId())
            ->update([
                'title'=>$resource->getTitle(),
                'description'=>$resource->getDescription(),
                'area_id'=>$resource->getAreaId(),
                'downloads'=>$resource->getDownloads(),
                'updated_at'=>$resource->getUpdatedAt()
            ]);
        return $this->findOrFail($resource->getResourceId());
    }
    
    public function updateState(Resource $resource): Resource
    {
        DB::table($this->table)
            ->where('resource_id', $resource->getResourceId())
            ->update([
                'state_id'=>$resource->getStateId(),
                'updated_at'=>$resource->getUpdatedAt()
            ]);
        return $this->findOrFail($resource->getResourceId());
    }

}
