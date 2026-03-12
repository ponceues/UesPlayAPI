<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IResourceStateRepository;
use UesPlay\Domain\Entities\ResourceState;
use UesPlay\Domain\Mappers\ResourceStateMapper;

class ResourceStateRepository implements IResourceStateRepository {
    
    private readonly string $table;
    
    public function __construct() {
        $this->table = 'resource_states';
    }

    
    public function fetch(Filter $filter): Collection {
        $query = DB::table($this->table)
                ->where('state_id','<>',null);
        
        $raw = $query->orderBy('created_at','desc') 
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return ResourceStateMapper::fromRawToCollection($raw);
    }

    public function findByCode(string $code): ResourceState {
        $raw = DB::table($this->table)
                ->where('code',$code)
                ->first();
        return ResourceStateMapper::fromRawToEntity($raw);
    }

    public function findById(string $stateId):ResourceState {
        $raw = DB::table($this->table)
                ->where('state_id',$stateId)
                ->first();
        return ResourceStateMapper::fromRawToEntity($raw);
    }
}
