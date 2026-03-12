<?php
namespace UesPlay\Infrastructure\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UesPlay\Domain\Interfaces\IVersionPlatformRepository;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\PlatformMapper;

class VersionPlatformRepository implements IVersionPlatformRepository
{
    private readonly string $table ;
    
    public function __construct()
    {
        $this->table = 'version_platforms';
    }
    public function fetchByVersion(string $versionId, Filter $filter): Collection
    {
        $query = DB::table($this->table)
        ->join('platforms', 'platforms.platform_id', '=', "{$this->table}.platform_id")
        ->where("{$this->table}.version_id", $versionId);
        
        
        $raw = $query->orderBy("{$this->table}.created_at",'desc')
        ->offset($filter->getPage() * $filter->getPageSize())
        ->limit($filter->getPageSize())
        ->get();
        
        return PlatformMapper::fromRawToCollection($raw);
        
    }

    public function insert(string $versionId, string $platformId): bool
    {
        DB::table($this->table)->insert([
            'version_id' => $versionId,
            'platform_id' => $platformId,
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

