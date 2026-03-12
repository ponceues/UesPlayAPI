<?php

namespace UesPlay\Domain\Services;

use Exception;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Interfaces\IMenuRepository;

class MenuService {
    
    private readonly IMenuRepository $menuRepository;    

    public function __construct(IMenuRepository $menuRepository) {
        $this->menuRepository = $menuRepository;
    }
    
    public function fetchMenus():Envelop{
        try{
            $result = new Envelop();                    
            $filter = new Filter();
            $filter->setPageSize(10000);
            
            $menus = $this->menuRepository->fetchMenus($filter);
            
            $result->setData($menus, $filter, $menus->count(), "menus");
            
            return $result;
        } catch (Exception $ex) {

        }
    }
}
