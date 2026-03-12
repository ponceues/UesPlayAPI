<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use UesPlay\Domain\Interfaces\IVersionRepository;
use UesPlay\Domain\Entities\Version;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\VersionMapper;

class VersionRepository implements IVersionRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'versions';
    }
    
    public function find(string $versionId): Version
    {
        $raw = DB::table($this->table)
            ->where('version_id', $versionId)
            ->first();
        return VersionMapper::fromRawToEntity($raw);
    }
    
    public function update(Version $version): Version
    {
        DB::table($this->table)
            ->where('version_id', $version->getVersionId())
            ->update([
                'name' => $version->getName(),
                'description' => $version->getDescription(),
                'source_url' => $version->getSourceUrl()
            ]);
        return $this->find($version->getVersionId());
    }
    
    public function insert(Version $version): Version
    {
        DB::table($this->table)
            ->insert([
                'version_id' => $version->getVersionId(),
                'resource_id' => $version->getResourceId(),
                'version' => $version->getVersion(),
                'description' => $version->getDescription(),
                'file_name' => $version->getFileName(),
                'license_id' => $version->getLicenseId(),
                'created_at' => Carbon::now('utc')
            ]);
        return $this->find($version->getVersionId());
    }

    public function fetchByResource(string $resourceId, Filter $filter): Collection
    {
        $query =  DB::table($this->table)
                ->where('deleted',false)
                ->where('resource_id',$resourceId);
        
        $raw = $query->orderBy('created_at','desc')
                    ->offset($filter->getPage()*$filter->getPageSize())
                    ->limit($filter->getPageSize())
                    ->get();
        return VersionMapper::fromRawToCollection($raw);
    }

    public function delete(Version $version): Version
    {
        DB::table($this->table)
            ->where('version_id', $version->getVersionId())
            ->update([
                'deleted' => true,
                'updated_at' => Carbon::now('utc')
            ]);
        return $this->find($version->getVersionId());
    }

    public function countByResource(string $resourceId, Filter $filter): int
    {
        $query =  DB::table($this->table)
                ->where('deleted',false)
                ->where('resource_id',$resourceId);
        
        return $query->count();
    }
    
    public function findLastByResource(string $resourceId): Version
    {
        $raw = DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('deleted', false)
            ->orderBy('created_at', 'desc')
            ->first();
        return VersionMapper::fromRawToEntity($raw);
    }    
    
}

