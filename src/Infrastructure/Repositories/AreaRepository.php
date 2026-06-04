<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Mappers\AreaMapper;
use UesPlay\Domain\Helpers\Filter;

class AreaRepository implements IAreaRepository {
    
    private readonly string $table;
    private readonly string $relatedTable;

    public function __construct() {
        $this->table = 'areas';
        $this->relatedTable = 'user_areas';
    }
    
    public function countByCode(string $code): int {
        $count = DB::table($this->table)
                ->where('code',$code)
                ->count();
        return $count;
    }

    public function countForUpdate(Area $area): int {
        $count =  DB::table($this->table)
            ->where(function ($query) use ($area) {
                $query->where('name', $area->getName())
                    ->orWhere('code', $area->getCode());
                })
            ->where('area_id','<>',$area->getAreaId())
            ;
        return $count->count();
    }

    public function delete(string $areaId): bool {
         DB::table($this->table)
            ->where('area_id',$areaId)
            ->update([
                'deleted'=>true
            ]);
        return true;
    }

    public function fetchByFilter(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        if($filter->getCode() !== null){
            $query->where('code', $filter->getCode());
        }
        
        if($filter->getText() !== null){
             $query = $query->whereAny([
                'name'
            ],'like','%'.$filter->getText().'%');
        }
        
        if($filter->getAvailable() !== null){
             $query = $query->where('active',$filter->getAvailable());
        }
        
        
        $raw = $query->orderBy('created_at','desc')
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return AreaMapper::fromRawToCollection($raw);
    }

    public function findById(string $areaId): Area {
        $raw = DB::table($this->table)
                ->where('area_id',$areaId)
                ->where('deleted',false)
                ->first();
        return AreaMapper::fromRawToEntity($raw);
    }

    public function insert(Area $area): Area {
        DB::table($this->table)
            ->insert([
                'area_id'=>$area->getAreaId(),
                'code'=>$area->getCode(),
                'name'=>$area->getName(),
                'active'=>$area->getActive(),
                'deleted'=>$area->getDeleted(),
                'description'=>$area->getDescription(),
                'created_at'=>$area->getCreatedAt(),
                'updated_at'=>$area->getUpdatedAt()
            ]);
        return $this->findById($area->getAreaId());
    }

    public function update(Area $area): Area {
        DB::table($this->table)
            ->where('area_id',$area->getAreaId())
            ->update([
                'name'=>$area->getName(),
                'active'=>$area->getActive(),
                'description'=>$area->getDescription(),
                'updated_at'=>$area->getUpdatedAt()
            ]);
        return $this->findById($area->getAreaId());
    }

    public function countByFilter(Filter $filter):int {
        $query =  DB::table($this->table)
            ->where('deleted',false);
        
        if($filter->getCode() !== null){
            $query->where('code', $filter->getCode());
        }
        
        if($filter->getName() !== null){
            $query->where('name', $filter->getName());
        }
        
        if($filter->getAvailable() !== null){
            $query = $query->where('active',$filter->getAvailable());
        }
        
        return $query->count();
    }

    public function countForCreate(Area $entity): int {
         $count =  DB::table($this->table)
            ->where('code', $entity->getCode())
            ->orWhere('name', $entity->getName())
            ->count();

        return $count;
    }

    public function findByCode(string $code): Area {
        $raw = DB::table($this->table)
                ->where('code',$code)
                ->first();
        return AreaMapper::fromRawToEntity($raw);
    }
    
    public function fetchByUser(string $userId): Collection {
        $raw = DB::table($this->relatedTable)
                ->join($this->table, $this->table.'.area_id','=',$this->relatedTable.'.area_id')
                ->where($this->table.'.active',true)
                ->where($this->relatedTable.'.active',true)
                ->where($this->relatedTable.'.user_id',$userId)
                ->select($this->table.'.*')
                ->get();
        return AreaMapper::fromRawToCollection($raw);
    }
    public function fetchFullByUser(string $userId): Collection
    {
        $raw = DB::table($this->relatedTable)
        ->join($this->table, $this->table.'.area_id','=',$this->relatedTable.'.area_id')
        ->where($this->relatedTable.'.user_id',$userId)
        ->select($this->table.'.*')
        ->get();
        
        return AreaMapper::fromRawToCollection($raw);
    }

    public function countTotal(): int {
        return DB::table($this->table)
                ->where('deleted', false)
                ->count();
    }

    public function countActive(): int {
        return DB::table($this->table)
                ->where('deleted', false)
                ->where('active', true)
                ->count();
    }

}
