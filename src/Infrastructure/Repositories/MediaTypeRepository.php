<?php
namespace UesPlay\Infrastructure\Repositories;
use UesPlay\Domain\Interfaces\IMediaTypeRepository;
use UesPlay\Domain\Entities\MediaType;
use UesPlay\Domain\Helpers\Filter;
use Illuminate\Support\Facades\DB;
use UesPlay\Domain\Mappers\MediaTypeMapper;
use Illuminate\Support\Collection;


class MediaTypeRepository implements IMediaTypeRepository
{
    private readonly string $table;

    public function __construct() {
        $this->table = 'media_types';
    }

    public function count(Filter $filter): int {
        $query = DB::table($this->table)->where('deleted', false);
        
        
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
        
        $raw = $query->orderBy('created_at', 'desc')
            ->offset($filter->getPage() * $filter->getPageSize())
            ->limit($filter->getPageSize())
            ->get();
        return MediaTypeMapper::fromRawToCollection($raw);
    }

    public function find(string $typeId): MediaType {
        $raw = DB::table($this->table)
            ->where('type_id', $typeId)
            ->where('deleted', false)
            ->first();
        return MediaTypeMapper::fromRawToEntity($raw);
    }

    public function findByName(string $name, ?string $typeId): ?MediaType {
        $query = DB::table($this->table)
            ->where('name', $name)
            ->where('deleted', false);
        
        if ($typeId !== null) {
            $query->where('type_id', '<>', $typeId);
        }
        
        $raw = $query->first();
        return $raw ? MediaTypeMapper::fromRawToEntity($raw) : null;
    }

    public function insert(MediaType $mediaType): MediaType {
        DB::table($this->table)->insert([
            'type_id' => $mediaType->getTypeId(),
            'name' => $mediaType->getName(),
            'description' => $mediaType->getDescription(),
            'enabled' => $mediaType->isEnabled(),
            'deleted' => false,
            'created_at' => $mediaType->getCreatedAt(),
            'updated_at' => $mediaType->getUpdatedAt()
        ]);
        return $this->find($mediaType->getTypeId());
    }

    public function update(MediaType $mediaType): MediaType {
        DB::table($this->table)
            ->where('type_id', $mediaType->getTypeId())
            ->update([
                'name' => $mediaType->getName(),
                'description' => $mediaType->getDescription(),
                'enabled' => $mediaType->isEnabled(),
                'updated_at' => $mediaType->getUpdatedAt()
            ]);
        return $this->find($mediaType->getTypeId());
    }

    public function delete(string $typeId): bool {
        DB::table($this->table)
            ->where('type_id', $typeId)
            ->update(['deleted' => true]);
        return true;
    }
}