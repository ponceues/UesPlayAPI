<?php

namespace UesPlay\Infrastructure\Repositories;

use UesPlay\Domain\Interfaces\IUserAreasRepository;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Entities\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserAreasRepository implements IUserAreasRepository {
    
    private readonly string $table;

    public function __construct() {
        $this->table = "user_areas";
    }
    
    
    public function count(Area $area, User $user): int {
        return 0;
    }

    public function insert(Area $area, User $user): bool {
        DB::table($this->table)
            ->insert([
            'area_id'=>$area->getAreaId(),
            'user_id'=>$user->getUserId(),
            'created_at'=>Carbon::now('utc'),
            'active'=>true
        ]);
        
        return true;
    }
}
