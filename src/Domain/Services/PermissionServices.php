<?php

namespace UesPlay\Domain\Services;

use Exception;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IPermissionRepository;
use UesPlay\Domain\Helpers\Envelop;

class PermissionServices {
    private readonly IPermissionRepository $permissionRepository;
    
    public function __construct(IPermissionRepository $permissionRepository) {
        $this->permissionRepository = $permissionRepository;
    }

    public function fetchPermission(): Envelop{
        try{
            $filter = new Filter();
            $filter->setPageSize(1000);
            $result = new Envelop();
            
            $permissions = $this->permissionRepository->fetchByFilter($filter);
            $result->setData($permissions, $filter, $permissions->count(), "permissions");
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
}
