<?php

namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Interfaces\IEmailTemplateRepository;
use UesPlay\Domain\Entities\EmailTemplate;
use UesPlay\Domain\Mappers\EmailTemplateMapper;

class EmailTemplateRepository implements IEmailTemplateRepository {
    
    private readonly string $table;
    
    public function __construct() {
        $this->table = 'email_templates';
    }

    public function findByCode(string $code): EmailTemplate{
        $raw = DB::table($this->table)
                    ->where('code',$code)
                    ->first();
        return EmailTemplateMapper::fromRawToEntity($raw);
    }
}
