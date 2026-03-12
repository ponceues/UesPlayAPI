<?php

namespace UesPlay\Infrastructure\Repositories;

use DateTime;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IPermissionRepository;
use UesPlay\Domain\Mappers\PermissionMapper;
use UesPlay\Domain\Helpers\Filter;

class PermissionRepository implements IPermissionRepository {
    
    private readonly string $table;
    private readonly string $relatedTable;

    public function __construct() {
        $this->table = 'permissions';
        $this->relatedTable ='rol_permissions';
    }

    public function fetchByFilter(Filter $filter):Collection{
        $query = DB::table($this->table);
                
        $raw = $query->get();
        
        return PermissionMapper::fromRawToCollection($raw);
    }

    public function fetchByRol(string $rolId): Collection {
        $query = DB::table($this->relatedTable)
                    ->join($this->table,$this->relatedTable.'.permission_id','=',$this->table.'.permission_id')
                    ->where($this->relatedTable.'.rol_id',$rolId)
                    ->select($this->table.'.*');
        $raw= $query->get();
        
        return PermissionMapper::fromRawToCollection($raw);
    }

    public function addToRol(string $rolId, $permissionId, DateTime $createdAt): bool {
        DB::table($this->relatedTable)
               ->insert([
                   'rol_id'=>$rolId,
                   'permission_id'=>$permissionId,
                   'created_at'=>$createdAt
               ]);
        return true;
    }

    public function removeFromRol(string $rolId, string $permissionId): bool {
        DB::table($this->relatedTable)
            ->where('rol_id',$rolId)
            ->where('permission_id',$permissionId)
            ->delete();
        return true;
    }
}
