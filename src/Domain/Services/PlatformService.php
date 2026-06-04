<?php

namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Interfaces\IPlatformRepository;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\Platform;

class PlatformService {
    private readonly IPlatformRepository $platformRepository;
    
    public function __construct(IPlatformRepository $platformRepository) {
        $this->platformRepository = $platformRepository;
    }
    
    public function listPlatforms(Filter $filter):Envelop{
        try{
            $result = new Envelop();
            $filter->setAvailable(true);
            
            $count = $this->platformRepository->count($filter);
            $platforms = $this->platformRepository->fetch($filter);
            
            $result->setData($platforms, $filter, $count, "platforms");
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function create(Platform $entity):Platform{
        try{
            $current = $this->platformRepository->findByName($entity->getName());
            if($current != null){
                throw new BadRequestException('Ya existe una plataforma con ese nombre');
            }
            $entity->setPlatformId(Uuid::uuid4()->toString());
            $entity->setCreatedAt(Carbon::now('utc'));
            $entity->setUpdatedAt(Carbon::now('utc'));
            
            $result = $this->platformRepository->create($entity);
            
            return $result;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function delete(string $platformId):array{
        try{
            $res = $this->platformRepository->delete($platformId);
            return ['result'=>$res];
        }
        catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function fetchPlatforms(Filter $filter):Envelop{
        try{
            $filter->setAvailable(true);
            $result = new Envelop();
            $count = $this->platformRepository->count($filter);
            $platforms = $this->platformRepository->fetch($filter);
            
            $result->setData($platforms, $filter, $count, "platforms");
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function fetch(Filter $filter):Envelop{
        try{
            $result = new Envelop();
            if(!auth()->user()){
                $filter->setAvailable(true);
            }
            
            $count = $this->platformRepository->count($filter);
            $platforms = $this->platformRepository->fetch($filter);
            
            $result->setData($platforms, $filter, $count, "platforms");
            return $result;
        } catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }

    public function find(string $platformId):Envelop{
        try{
            $result = $this->platformRepository->find($platformId);
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function update(Platform $entity):Platform{
        try{
            $current = $this->platformRepository->countForUpdate($entity->getPlatformId(),$entity->getName());
            if($current != null){
                throw new BadRequestException('Ya existe otra plataforma con ese nombre');
            }
            
            $entity->setUpdatedAt(Carbon::now('utc'));
            
            $result = $this->platformRepository->update($entity);
            
            return $result;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function summaryPlatforms():array{
        try{
            $summary = $this->platformRepository->getSummary();
            return [
                'entity' => 'Plataformas',
                'total' => $summary['total'],
                'active' => $summary['active']
            ];
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    

}
