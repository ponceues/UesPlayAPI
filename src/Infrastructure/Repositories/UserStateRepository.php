<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IUserStateRepository;
use UesPlay\Domain\Entities\UserState;
use UesPlay\Domain\Mappers\UserStateMapper;
use UesPlay\Domain\Helpers\Filter;

class UserStateRepository implements IUserStateRepository {
    private readonly string $table;
    
    public function __construct() {
        $this->table = 'user_states';
    }

    public function fetch(Filter $filter): Collection {
        $raw = DB::table($this->table)
            ->where('state_id','<>', null)
            ->get();
        
        return UserStateMapper::fromRawToCollection($raw);
    }

    public function findByCode(string $code): UserState {
        $raw = DB::table($this->table)
                ->where('code',$code)
                ->first();
        return UserStateMapper::fromRawToEntity($raw);
    }


    public function findById(string $stateId): UserState {
        $raw = DB::table($this->table)
            ->where('state_id',$stateId)
            ->first();
        return UserStateMapper::fromRawToEntity($raw);
    }

    public function count(Filter $filter): int {
        $query = DB::table($this->table)
                ->where('state_id','<>',null);

        return $query->count();
    }
}
