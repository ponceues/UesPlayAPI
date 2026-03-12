<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\ILanguageRepository;
use UesPlay\Domain\Mappers\LanguageMapper;
use UesPlay\Domain\Entities\Language;

class LanguageRepository implements ILanguageRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'languages';
    }
    
    public function findOrDefault(string $languageId): ?Language
    {
        $raw = DB::table($this->table)
            ->where('language_id', $languageId)
            ->where('deleted', false)
            ->first();
        if($raw){
            return LanguageMapper::fromRaw($raw);
        }
        
        return null;
        
    }

    public function find(string $languageId): Language
    {
        $raw = DB::table($this->table)
            ->where('language_id', $languageId)
            ->where('deleted', false)
            ->first();
        
        return LanguageMapper::fromRaw($raw);
        
        
    }

    public function fetch(Filter $filter): Collection
    {
        $query =  DB::table($this->table)
                    ->where('deleted',false);
        
        $raw = $query->orderBy('created_at','desc')
        ->offset($filter->getPage()*$filter->getPageSize())
        ->limit($filter->getPageSize())
        ->get();
        return LanguageMapper::fromRawToCollection($raw);
    }

    public function count(Filter $filter): int
    {
        $count =  DB::table($this->table)
                    ->where('deleted',false)
                    ->count();
        
        return $count;
    }

    public function insert(Language $language): Language
    {
        DB::table($this->table)->insert([
            'language_id' => $language->getLanguageId(),
            'name' => $language->getName(),
            'code' => $language->getCode(),
            'created_at' => $language->getCreatedAt(),
            'deleted' => false,
        ]);
        return $this->find($language->getLanguageId());
    }

    public function update(Language $language): Language
    {
        DB::table($this->table)
            ->where('language_id', $language->getLanguageId())
            ->update([
                'name' => $language->getName(),
                'code' => $language->getCode(),
            ]);
        return $this->find($language->getLanguageId());
    }

    public function delete(string $languageId): bool
    {
        DB::table($this->table)
            ->where('language_id', $languageId)
            ->update([
                'deleted' => true,
            ]);
        return true;        
    }

}

