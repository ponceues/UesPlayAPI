<?php
namespace UesPlay\Domain\Services;

use Exception;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Interfaces\ILanguageRepository;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;

class LanguageService
{
    private readonly ILanguageRepository $languageRepository;
    
    public function __construct(ILanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }
    
    public function fetch(Filter $filter):Envelop
    {
        try {
            $res = new Envelop();
            $languages = $this->languageRepository->fetch($filter);
            $count = $this->languageRepository->count($filter);
            
            $res->setData($languages, $filter, $count, 'languages');
            
            return $res;
        } catch (Exception $e) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
        
    }
    
}

