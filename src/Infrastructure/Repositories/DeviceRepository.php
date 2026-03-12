<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IDeviceRepository;
use UesPlay\Domain\Entities\Device;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\DeviceMapper;

class DeviceRepository implements IDeviceRepository {
    private readonly string $table;
    
    public function __construct() {
        $this->table = 'devices';
    }

    
    public function countByFilter(Filter $filter): int {
        $query =  DB::table($this->table)
                ->where('deleted',false);
       
        if($filter->getAvailable() !== null){
            $query = $query->where('active',$filter->getActive());
        }
        
        if($filter->getText() !== null){
            $query = $query->whereAny([
                'name'
            ],'like','%'.$filter->getText().'%');
        }
        
        return $query->count();
    }

    public function fetchByFilter(Filter $filter): Collection {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        if($filter->getAvailable() !== null){
            $query = $query->where('active',$filter->getAvailable());
        }
        
        if($filter->getText() !== null){
            $query = $query->whereAny([
                'name'
            ],'like','%'.$filter->getText().'%');
        }
        
        $raw = $query->orderBy('name','desc') 
                ->offset($filter->getPage()*$filter->getPageSize())
                ->limit($filter->getPageSize())
                ->get();
        return DeviceMapper::fromRawToCollection($raw);
    }

    public function findById(string $id): Device {
        $raw = DB::table($this->table)
                ->where('device_id',$id)
                ->first();
        return DeviceMapper::fromRawToEntity($raw);
    }

    public function create(Device $device): Device {
        DB::table($this->table)
            ->insert([
                'device_id'=>$device->getDeviceId(),
                'name'=>$device->getName(),
                'description'=>$device->getDescription(),
                'active'=>$device->getActive(),
                'icon'=>$device->getIcon(),
                'created_at'=>$device->getCreatedAt(),
                'updated_at'=>$device->getUpdatedAt()
            ]);
        return $this->findById($device->getDeviceId());
    }

    public function delete(string $deviceId): bool {
        DB::table($this->table)
            ->where('device_id',$deviceId)
            ->update([
                'deleted'=>true
            ]);
        return true;
    }

    public function findByName(string $name): ?Device {
        $raw = DB::table($this->table)
            ->where('name',$name)
            ->first();
        if($raw === null){
            return null;
        }
        return DeviceMapper::fromRawToEntity($raw);
    }

    public function findOrDefault(string $id): ?Device {
        $raw = DB::table($this->table)
                ->where('device_id',$id)
                ->first();
        if($raw === null){
            return null;
        }
        return DeviceMapper::fromRawToEntity($raw);
    }

    public function update(Device $device): Device {
        DB::table($this->table)
            ->where('device_id',$device->getDeviceId())
            ->update([
                'name'=>$device->getName(),
                'description'=>$device->getDescription(),
                'active'=>$device->getActive(),
                'icon'=>$device->getIcon(),
                'created_at'=>$device->getUpdatedAt()
            ]);
        return $this->findById($device->getDeviceId());
    }

    public function countForUpdate(string $deviceId, string $name): int {
        $count  = DB::table($this->table)
                ->where('name',$name)
                ->where('device_id','<>',$deviceId)
                ->count();
        return $count;
    }
}
