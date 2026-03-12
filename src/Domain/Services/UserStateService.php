<?php

namespace UesPlay\Domain\Services;

use Exception;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Interfaces\IUserStateRepository;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;

class UserStateService {
    
    private readonly IUserStateRepository $userStateRepository;
    
    public function __construct(IUserStateRepository $userStateRepository) {
        $this->userStateRepository = $userStateRepository;
    }

    function fetchUserStates(Filter $filter): Envelop {
        try{
            $result = new Envelop();
            $count = $this->userStateRepository->count($filter);
            $data = $this->userStateRepository->fetch($filter);

            $result->setData($data, $filter, $count,'states');
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
}
