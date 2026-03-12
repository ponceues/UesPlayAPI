<?php

namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;


use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Helpers\Envelop;

class ResourceAreaService {
    private readonly IAreaRepository $areaRepository;
    
    public function __construct(IAreaRepository $areaRepository) {
        $this->areaRepository = $areaRepository;
    }

    public function createArea(Area $area):Area{
        try{
            $count = $this->areaRepository->countForCreate($area);

            if($count > 0){
                throw new BadRequestException('El codigo/nombre ya se encuentra en uso.');
            }
            $area->setAreaId(Uuid::uuid4()->toString());
            $area->setCreatedAt(Carbon::now('utc'));
            $area->setUpdatedAt(Carbon::now('utc'));
            $area->setDeleted(false);
            
            $res = $this->areaRepository->insert($area);
            return $res;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception) {
            throw new InternalErrorException("Ha ocuurrido un error inesperado.");
        }
    }
    
    public function fetchByFilter(Filter $filter):Envelop{
        try {
            $res = new Envelop();
            $filter->setAvailable(true);
            $data = $this->areaRepository->fetchByFilter($filter);
            $count = $this->areaRepository->countByFilter($filter);
            $res->setData($data, $filter, $count,'areas');
            return $res;
        } catch(Exception){
            throw new InternalErrorException("Ha ocuurrido un error inesperado.");
        }
    }
    
    public function fetchForView(Filter $filter):Envelop{
        try {
            $res = new Envelop();
            
            $data = $this->areaRepository->fetchByFilter($filter);
            $count = $this->areaRepository->countByFilter($filter);
            $res->setData($data, $filter, $count,'areas');
            return $res;
        } catch(Exception){
            throw new InternalErrorException("Ha ocuurrido un error inesperado.");
        }
    }
    
    public function updateArea(Area $area): Area{
        try {            
            $count = $this->areaRepository->countForUpdate($area);
            if($count > 0){
                throw new BadRequestException('El codigo/nombre ya se encuentra en uso.');
            }
            
            $area->setUpdatedAt(Carbon::now('utc'));
            $res = $this->areaRepository->update($area);
            return $res;
        } catch(Exception){
            throw new InternalErrorException("Ha ocuurrido un error inesperado.");
        }
    }
    
    public function deleteArea(string $areaId):bool{
        try{
            $res = $this->areaRepository->delete($areaId);
            return $res;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocuurrido un error inesperado.");
        }
    }
            
}
