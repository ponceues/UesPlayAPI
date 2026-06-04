<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;
use UesPlay\Domain\Interfaces\IUserRepository;
use UesPlay\Domain\Entities\User;
use UesPlay\Domain\Mappers\UserMapper;
use UesPlay\Domain\Helpers\Filter;

class UserRepository implements IUserRepository {
    private readonly  string $table;
    private readonly string $areaTable;
    public function __construct() {
        $this->table = 'users';
        $this->areaTable = "user_areas";
    }
    
    public function insert(User $user): User {
        DB::table($this->table)->insert([
            'user_id'=>$user->getUserId(),
            'state_id'=>$user->getState()->getStateId(),
            'rol_id'=>$user->getRolId(),
            'name'=>$user->getName(),
            'email'=>$user->getEmail(),
            'password'=>$user->getPassword(),
            'created_at'=>$user->getCreatedAt(),
            'updated_at'=>$user->getUpdatedAt()
        ]);
        return $this->findById($user->getUserId());
    }

    public function findById(string $userId): User {
        $raw = DB::table($this->table)
                    ->where('user_id',$userId)
                    ->first();
        return UserMapper::fromRawToEntity($raw);
    }

    public function searchByFilter(Filter $filter) {
        
        $query =  DB::table($this->table)->where('user_id','<>',null);
        
        if($filter->getName() !== null){
            $query = $query->where('name',$filter->getName());
        }
        
        if($filter->getStateId() !== null){
            $query = $query->where('state_id',$filter->getStateId());
        }
        
        if($filter->getText() !== null){           
            $query = $query->whereAny([
                'name',
                'email'
            ],'like','%'.$filter->getText().'%');
        }
        
        $raw = $query->orderBy('created_at','desc')
            ->offset($filter->getPage()*$filter->getPageSize())
            ->limit($filter->getPageSize())
            ->get();
        
        return UserMapper::fromRawToCollection($raw);
    }

    public function countByFilter(Filter $filter): int {
        $query =  DB::table($this->table)->where('user_id','<>',null);
        
        if($filter->getName() !== null){
            $query = $query->where('name',$filter->getName());
        }
        
        if($filter->getStateId() !== null){
            $query = $query->where('state_id',$filter->getStateId());
        }
        
        if($filter->getText() !== null){
            $query = $query->whereAny([
                'name',
                'email'
            ],'like','%'.$filter->getText().'%');
        }
        
        if($filter->getEmail() !== null){
            $query = $query->where('email',$filter->getEmail());
        }
        
        $count = $query->count();
        
        return $count;
    }
    
    public function updateUser(User $user): User {
        DB::table($this->table)
                ->where('user_id',$user->getUserId())
                ->update([
                    'state_id'=>$user->getStateId(),
                    'rol_id'=>$user->getRolId(),
                    'name'=>$user->getName(),
                    'created_at'=>$user->getCreatedAt()
            ]);
        return $this->findById($user->getUserId());
    }
    
    public function addArea(string $userId, string $areaId, DateTime $createdAt): bool {
        DB::table($this->areaTable)
                ->insert([
                    "area_id"=>$areaId,
                    "user_id"=>$userId,
                    "active"=>true,
                    "created_at"=>$createdAt
                ]);
        return true;
    }

    public function findByEmail(string $email): ?User {
        $raw = DB::table($this->table)
            ->where('email',$email)
            ->first();
        if($raw === null){
            return null;
        }
        return UserMapper::fromRawToEntity($raw);
    }

    public function completeAccount(string $userId, string $stateId, string $password): bool {
        DB::table($this->table)
            ->where('user_id',$userId)
            ->update([
                'state_id'=>$stateId,
                'email_verified_at'=> Carbon::now(),
                'verify_code'=>null,
                'password'=>$password
            ]
        );
        return true;
    }

    public function insertWithCode(User $user, string $code): bool {
        DB::table($this->table)->insert([
            'user_id'=>$user->getUserId(),
            'state_id'=>$user->getState()->getStateId(),
            'rol_id'=>$user->getRolId(),
            'verify_code'=>$code,
            'name'=>$user->getName(),
            'email'=>$user->getEmail(),
            'password'=>$user->getPassword(),
            'created_at'=>$user->getCreatedAt(),
            'updated_at'=>$user->getUpdatedAt()
        ]);
        return true;
    }

    public function updatePassword(string $userId, string $password): bool {
        DB::table($this->table)
                ->where('user_id',$userId)
                
                ->update([
                    'password'=>$password,
                    'verify_code'=>null,
                    'verify_mode'=>null,
                    'verify_date'=>null
        ]);
                return true;
    }

    public function verfifyAccount(string $identity, string $verifyCode): ?User {
        $raw = DB::table($this->table)
            ->where('user_id',$identity)
            ->where('verify_code',$verifyCode)
            ->where('email_verified_at',null)
            ->first();
        if($raw === null){
            return null;
        }
        return UserMapper::fromRawToEntity($raw);
    }

    public function disableAllAreas(string $userId): bool {
        DB::table($this->areaTable)
                ->where('user_id',$userId)
                ->update(
                    [
                        'active'=>false
                    ]
                );
        return true;
    }
    
    public function activateArea(string $userId, string $areaId): bool
    {
        DB::table($this->areaTable)
                ->where('user_id',$userId)
                ->where('area_id',$areaId)
                ->update(
                    [
                        'active'=>true
                    ]
                );
        return true;
    }
    public function updateSecurity(string $userId, string $sercurityCode, string $mode): bool
    {
        DB::table($this->table)
        ->where('user_id',$userId)
        ->update([
            'verify_code'=>$sercurityCode,
            'verify_mode'=>$mode,
            'verify_date'=>Carbon::now('utc')
        ]);
        return true;
    }
    
    public function getUserData(string $userId, string $sercurityCode, string $mode)
    {
        $raw = DB::table($this->table)
            ->where('user_id',$userId)
            ->where('verify_code',$sercurityCode)
            ->where('verify_mode',$mode)
            ->select('user_id','verify_date')
            ->first();
            if($raw === null){
                return null;
            }
        return $raw;
    }
    public function verfifyAccountStep(string $identity, string $verifyCode, string $step): ?User
    {
        $raw = DB::table($this->table)
                    ->where('user_id',$identity)
                    ->where('verify_code',$verifyCode)
                    ->where('verify_mode',$step)
                    ->first();
        if($raw === null){
            return null;
        }
        return UserMapper::fromRawToEntity($raw);
    }




}
