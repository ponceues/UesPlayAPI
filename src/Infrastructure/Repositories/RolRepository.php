<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IRolRepository;
use UesPlay\Domain\Entities\Rol;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\RolMapper;

class RolRepository implements IRolRepository {

    private readonly string $table;

    public function __construct() {
        $this->table = "roles";
    }
    
    public function createRol(Rol $rol): Rol {
        DB::table($this->table)
            ->insert([
                'rol_id'=>$rol->getRolId(),
                'name'=>$rol->getName(),
                'description'=>$rol->getDescription(),
                'is_active'=>$rol->getIsActive(),
                'is_default'=>false,
                'created_at'=>$rol->getCreatedAt(),
                'updated_at'=>$rol->getUpdateAt()
            ]);
        return $this->findById($rol->getRolId());
    }

    public function fetchRoles(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('is_deleted',false);
        
        if($filter->getAvailable() !== null){
            $query = $query->where('is_active',$filter->getAvailable());
        }
        
        $raw = $query->orderBy('created_at','desc') 
                ->offset(value: $filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return RolMapper::fromRawToCollection($raw);
    }

    public function updateRol(Rol $rol):Rol {
        DB::table($this->table)
            ->where('rol_id',$rol->getRolId())
            ->update([
                'name'=>$rol->getName(),
                'description'=>$rol->getDescription(),
                'is_active'=>$rol->getIsActive(),
                'updated_at'=>$rol->getUpdateAt()
            ]);

        return $this->findById($rol->getRolId());
    }

    public function findById(string $rolId): Rol {
        $raw = DB::table($this->table)
                ->where('rol_id',$rolId)
                ->where('is_deleted',false)
                ->first();
        return RolMapper::fromRawToEntity($raw);
    }

    public function countByFilter(Filter $filter): int {
        $query = DB::table($this->table)
                ->where('is_deleted',false);
        if($filter->getName() != null){
            $query->where('name',$filter->getName());
        }
        
        if($filter->getAvailable() !== null){
            $query = $query->where('is_active',$filter->getAvailable());
        }
        return $query->count();
    }

    public function countForUpdate(Filter $filter): int {
        $query = DB::table($this->table)
                ->where('rol_id','<>',$filter->getId());
        if($filter->getName() != null){
            $query = $query->where('name',$filter->getName());
        }
        return $query->count();
    }

    public function deleteRol(string $rolId): bool {
        DB::table($this->table)
            ->where('rol_id',$rolId)
            ->update([
                'is_deleted'=>true
            ]);

        return true;
    }
}
