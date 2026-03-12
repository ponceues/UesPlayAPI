<?php

namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Entities\ResourceType;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IResourceTypeRepository;

class ResourceTypeService {
   
    private readonly IResourceTypeRepository $typeRepository;
    
    public function __construct(IResourceTypeRepository $typeRepository) {
        $this->typeRepository = $typeRepository;
    }

    public function createResourceType(ResourceType $entity): ResourceType{
        try {
            $count = $this->typeRepository->countForCreate($entity);
            if($count > 0){
                throw new BadRequestException("El nombre ya se encuentra en uso");
            }
            
            $entity->setDeleted(false);
            $entity->setTypeId(Uuid::uuid4()->toString());
            $entity->setDeleted(false);
            $entity->setCreatedAt(Carbon::now('utc'));
            $entity->setUpdatedAt(Carbon::now('utc'));
            $res = $this->typeRepository->insert($entity);
            
            return $res;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function fetchResourceTypes(Filter $filter):Envelop {
        try {
            $result = new Envelop();
            $count = $this->typeRepository->countByFilter($filter);
            $resourceTypes = $this->typeRepository->fetchByFilter($filter);
            
            $result->setData($resourceTypes, $filter, $count,'types');
            return $result;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function updateResourceType(ResourceType $entity): ResourceType{
        try {
            $count = $this->typeRepository->countForUpdate($entity);
            if($count > 0){
                throw new BadRequestException("El nombre ya se encuentra en uso");
            }
            $entity->setUpdatedAt(Carbon::now('utc'));
            $res = $this->typeRepository->update($entity);
            
            return $res;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function delelteResourceType(string $id): bool{
        try {
            $res = $this->typeRepository->delete($id);
            return $res;
        }
        catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
}
