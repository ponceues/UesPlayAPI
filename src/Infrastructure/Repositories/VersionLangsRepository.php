<?php
namespace UesPlay\Infrastructure\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UesPlay\Domain\Interfaces\IVersionLangsRepository;

use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Mappers\LanguageMapper;
use UesPlay\Domain\Entities\Language;
use UesPlay\Domain\Entities\Version;


class VersionLangsRepository implements IVersionLangsRepository
{
    private readonly string $table;
    
    public function __construct()
    {
        $this->table = 'version_langs';
    }
    
    public function fetchByVersion(string $versionId, Filter $filter): Collection
    {
        $query = DB::table($this->table)
        ->join('languages', 'languages.language_id', '=', "{$this->table}.lang_id")
        ->where("{$this->table}.version_id", $versionId);
        
        
        $raw = $query->orderBy("{$this->table}.created_at",'desc')
            ->offset($filter->getPage() * $filter->getPageSize())
            ->limit($filter->getPageSize())
            ->get();

        return LanguageMapper::fromRawToCollection($raw);
        
    }

    public function removeForVersion(string $versionId): bool
    {
        return DB::table($this->table)
            ->where('version_id', $versionId)
            ->delete() > 0;
        
        return true;
    }

    public function insert(Version $version, Language $language): bool
    {
        DB::table($this->table)
            ->insert([
                'version_id' => $version->getVersionId(),
                'lang_id' => $language->getLanguageId(),
                'created_at' => Carbon::now('utc'),
            ]);
       return true;
    } 
}

