<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use DateTime;

use UesPlay\Domain\Interfaces\IMenuRepository;
use UesPlay\Domain\Mappers\MenuMapper;
use UesPlay\Domain\Helpers\Filter;




class MenuRepository implements IMenuRepository {
    
    private readonly string $table;
    private readonly string $relatedTable;
    
    public function __construct() {
        $this->table = 'menus';
        $this->relatedTable = 'rol_menus';
    }

    
    public function fetchMenus(Filter $filter): Collection {
        
        $query = DB::table($this->table)
                    ->where('menu_id','<>',null);        
        $raw = $query->orderBy('name')
                ->get();
        
        return MenuMapper::fromRawToCollection($raw);
    }

    public function fetchByRol(string $rolId): Collection {
        $query = DB::table($this->relatedTable)
                    ->join($this->table,$this->table.'.menu_id','=',$this->relatedTable.'.menu_id')
                    ->where($this->relatedTable.'.rol_id',$rolId)
                    ->select($this->table.'.*');
        $raw= $query->get();
        
        return MenuMapper::fromRawToCollection($raw);
    }

    public function addMenuToRol(string $rolId, string $menuId,DateTime $createdAt):bool {
        DB::table($this->relatedTable)
               ->insert([
                   'rol_id'=>$rolId,
                   'menu_id'=>$menuId,
                   'created_at'=>$createdAt
               ]);
        return true;
    }
    
    public function removeMenuFromRol(string $rolId, string $menuId):bool {
        DB::table($this->relatedTable)
            ->where('rol_id',$rolId)
            ->where('menu_id',$menuId)
            ->delete();
        return true;
    }
}
