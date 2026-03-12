<?php

namespace UesPlay\Domain\Services;

use Exception;

use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IResourceStateRepository;
use UesPlay\Domain\Helpers\Envelop;

class ResourceStateService {
    
    private readonly IResourceStateRepository $stateRepository;

    public function __construct(IResourceStateRepository $stateRepository) {
        $this->stateRepository = $stateRepository;
    }

    public function fetchStates(Filter $filter):Envelop{
        try{
            $envelope = new Envelop();
            
            $res = $this->stateRepository->fetch($filter);
            $envelope->setData($res,$filter,$res->count(),'states');
            
            return $envelope;
        } catch (Exception) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }
}
