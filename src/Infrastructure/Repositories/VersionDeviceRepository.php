<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use UesPlay\Domain\Interfaces\IVersionDeviceRepository;
use UesPlay\Domain\Entities\Device;
use UesPlay\Domain\Entities\Version;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\DeviceMapper;

class VersionDeviceRepository implements IVersionDeviceRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'version_devices';
    }
    
    public function fetchForVersion(string $versionId, Filter $filter): Collection
    {
        $query = DB::table($this->table)
                    ->join('devices', 'devices.device_id', '=', "{$this->table}.device_id")
                    ->where("{$this->table}.version_id", $versionId);
        $raw = $query->orderBy("{$this->table}.created_at",'desc')
        ->offset($filter->getPage() * $filter->getPageSize())
        ->limit($filter->getPageSize())
        ->get();
                    
        return DeviceMapper::fromRawToCollection($raw);
            
    }

    public function insert(Version $version, Device $device): bool
    {
        DB::table($this->table)->insert([
            'version_id' => $version->getVersionId(),
            'device_id' => $device->getDeviceId(),
            'created_at' => Carbon::now('utc')
        ]);
        
        return true;
    }

    public function bulkRemove(string $versionId): bool
    {
        DB::table($this->table)
            ->where('version_id', $versionId)
            ->delete();
        
        return true;
    }

}

