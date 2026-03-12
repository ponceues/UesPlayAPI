<?php
namespace UesPlay\Infrastructure\Repositories;
use UesPlay\Domain\Interfaces\IMediaGenreRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UesPlay\Domain\Entities\MediaGenre;
use UesPlay\Domain\Mappers\MediaGenreMapper;
use UesPlay\Domain\Helpers\Filter;

class MediaGenreRepository implements IMediaGenreRepository
{
    private readonly string $table;

    public function __construct() {
        $this->table = 'media_genres';
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
        
        if($filter->getMediaTypeId() !== null){
            $query->where('type_id', $filter->getMediaTypeId());
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
        
        if($filter->getMediaTypeId() !== null){
            $query->where('type_id', $filter->getMediaTypeId());
        }
        
        $result = $query->get();
        return MediaGenreMapper::fromRawToCollection($result);
    }

    public function find(string $mediaGenreId): MediaGenre {
        $raw = DB::table($this->table)
            ->where('genre_id', $mediaGenreId)
            ->where('deleted', false)
            ->first();
        return MediaGenreMapper::fromRawToEntity($raw);
    }

    public function findByName(string $name, ?string $mediaGenreId ): ?MediaGenre {
        $query = DB::table($this->table)
            ->where('name', $name)
            ->where('deleted', false);
        if ($mediaGenreId !== null) {
            $query->where('genre_id','<>', $mediaGenreId);
        }
        $raw = $query->first();
        return $raw ? MediaGenreMapper::fromRawToEntity($raw) : null;
    }

    public function insert(MediaGenre $mediaGenre): MediaGenre {
        DB::table($this->table)->insert([
            'genre_id' => $mediaGenre->getGenreId(),
            'type_id' => $mediaGenre->getMediaTypeId(),
            'name' => $mediaGenre->getName(),
            'enabled' => $mediaGenre->isEnabled(),
            'description'=>$mediaGenre->getDescription(),
            'deleted' => false,
            'created_at' => $mediaGenre->getCreatedAt(),
            'updated_at' => $mediaGenre->getUpdatedAt(),           
        ]);
        return $this->find($mediaGenre->getGenreId());
    }

    public function update(MediaGenre $mediaGenre): MediaGenre {
        DB::table($this->table)
            ->where('genre_id', $mediaGenre->getGenreId())
            ->update([
                'type_id' => $mediaGenre->getMediaTypeId(),
                'name' => $mediaGenre->getName(),
                'enabled' => $mediaGenre->isEnabled(),
                'updated_at' => $mediaGenre->getUpdatedAt(),
            ]);
        return $this->find($mediaGenre->getGenreId());
    }

    public function delete(string $mediaGenreId): bool {
        $affected = DB::table($this->table)
            ->where('genre_id', $mediaGenreId)
            ->update(['deleted' => true]);
        return $affected > 0;
    }
}