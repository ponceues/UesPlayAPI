<?php
namespace UesPlay\Infrastructure\Repositories;
use UesPlay\Domain\Interfaces\ILicenseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UesPlay\Domain\Entities\License;
use UesPlay\Domain\Mappers\LicenseMapper;
use UesPlay\Domain\Helpers\Filter;

class LicenseRepository implements ILicenseRepository
{
    private readonly string $table;

    public function __construct() {
        $this->table = 'licenses';
    }

    public function count(Filter $filter): int {
        $query = DB::table($this->table)
            ->where('deleted', false);
        
        if ($filter->getText() !== null) {
            $query->where('name', 'like', '%' . $filter->getText() . '%');
        }
        
        if ($filter->getEnabled() !== null) {
            $query->where('enabled', $filter->getEnabled());
        }
        
        return $query->count();
    }

    public function search(Filter $filter): Collection {
        $query = DB::table($this->table)->where('deleted', false);
        
        if ($filter->getText() !== null) {
            $query->where('name', 'like', '%' . $filter->getText() . '%');
        }
        
        if ($filter->getEnabled() !== null) {
            $query->where('enabled', $filter->getEnabled());
        }
        
        $result = $query->get();
        return LicenseMapper::fromRawToCollection($result);
    }

    public function find(string $licenceId): License {
        $raw = DB::table($this->table)
            ->where('license_id', $licenceId)
            ->where('deleted', false)
            ->first();
        return LicenseMapper::fromRawToEntity($raw);
    }

    public function insert(License $license): License {

        DB::table($this->table)->insert([
            'license_id' => $license->getLicenseId(),
            'name' => $license->getName(),
            'version' => $license->getVersion(),
            'description' => $license->getDescription(),
            'enabled' => $license->isEnabled(),
            'deleted' => false,
            'created_at' => $license->getCreatedAt(),
            'updated_at' => $license->getUpdatedAt()
            
        ]);
        return $this->find($license->getLicenseId());
    }

    public function update(License $licence): License {
        DB::table($this->table)
            ->where('license_id', $licence->getLicenseId())
            ->update([
                'name' => $licence->getName(),
                'version' => $licence->getVersion(),
                'description' => $licence->getDescription(),
                'enabled' => $licence->isEnabled(),
                'updated_at' => $licence->getUpdatedAt()
            ]);
        return $this->find($licence->getLicenseId());
    }

    public function delete(string $licenceId): bool {
        DB::table($this->table)
            ->where('license_id', $licenceId)
            ->update(['deleted' => true]);
        return true;
    }
    
    public function findByName(string $name, ?string $licenceId = null): ?License {
        $query = DB::table($this->table)
            ->where('name', $name)
            ->where('deleted', false);
            if ($licenceId !== null) {
                $query->where('license_id', '!=', $licenceId);
            }
            
            $raw = $query->first();
        if ($raw) {
            return LicenseMapper::fromRawToEntity($raw);
        }
        return null;
    }
    
    public function getSummary(): array {
        $totalNotDeleted = DB::table($this->table)
            ->where('deleted', false)
            ->count();
        
        $totalActive = DB::table($this->table)
            ->where('deleted', false)
            ->where('enabled', true)
            ->count();
        
        return [
            'total' => $totalNotDeleted,
            'active' => $totalActive
        ];
    }
}