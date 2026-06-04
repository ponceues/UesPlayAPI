<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Entities\Rol;
use UesPlay\Domain\Interfaces\IPermissionRepository;
use UesPlay\Domain\Interfaces\IMenuRepository;
use UesPlay\Domain\Interfaces\IRolRepository;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;

class RolService {
    
    private readonly IRolRepository $rolRepository;
    private readonly IMenuRepository $menuRepository;
    private readonly IPermissionRepository $permissionRepository;


    public function __construct(IRolRepository $rolRepository,
                                IMenuRepository $menuRepository, 
                                IPermissionRepository $permissionRepository) {
        $this->rolRepository = $rolRepository;
        $this->permissionRepository = $permissionRepository;
        $this->menuRepository = $menuRepository;
    }

    public function createRol(Rol $rol):Rol{
        try{
            $filter = new Filter();
            $filter->setName($rol->getName());
            
            if($this->rolRepository->countByFilter($filter) > 0){
                throw new BadRequestException('Ya existe un rol con el nombre especificado');
            }
            
            $rol->setRolId(Uuid::uuid4()->toString());
            $rol->setIsDefault(false);
            $rol->setIsDelete(false);
            $rol->setCreatedAt(Carbon::now('utc'));
            $rol->setUpdateAt(Carbon::now('utc'));
            
            $result = $this->rolRepository->createRol($rol);
            return $result;
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public  function delteRol(string $rolId):bool{
        try{
            $rolToDelete= $this->rolRepository->findById($rolId);
            if($rolToDelete->getIsDefault()){
                throw new BadRequestException('El rol no puede ser moficado');
            }
            if($rolToDelete->getIsDelete()){
                throw new BadRequestException('El rol especificado no existe');
            }
            $result = $this->rolRepository->deleteRol($rolId);
            return $result;
        }   
        catch (NotFoundException $ex){
            throw $ex;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function fetchForView(Filter $filter):Envelop {
        try{
            $result = new Envelop();
            $filter->setAvailable(true);
            $count = $this->rolRepository->countByFilter($filter);
            $roles = $this->rolRepository->fetchRoles($filter);
            
            $result->setData($roles, $filter, $count,'roles');
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    
    public function fetchRoles(Filter $filter):Envelop {
        try{
            $result = new Envelop();
            $count = $this->rolRepository->countByFilter($filter);
            $roles = $this->rolRepository->fetchRoles($filter);
            
            $result->setData($roles, $filter, $count,'roles');
            return $result;
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function findRolById(string $rolId):Rol{
        try{
            $result = $this->rolRepository->findById($rolId);
            $permissions = $this->permissionRepository->fetchByRol($rolId);
            $menus = $this->menuRepository->fetchByRol($rolId);
            
            $result->setMenus($menus);
            $result->setPermissions($permissions);
            return $result;
        } 
        catch (NotFoundException $ex){
            throw  new NotFoundException('La entidad {Rol} buscada no existe.');
        } 
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function updateRol(Rol $rolToUpdate):Rol{
        try{
            $filter = new Filter();
            $filter->setName($rolToUpdate->getName());
            $filter->setId($rolToUpdate->getRolId());

            if($this->rolRepository->countForUpdate($filter) > 0){
                throw new BadRequestException('El nombre especificado ya esta en uso');
            }
            $updatedRol = $this->rolRepository->findById($rolToUpdate->getRolId());
            
            if($updatedRol->getIsDefault()){
                throw new BadRequestException('El rol no puede ser modificado');
            }
            $updatedRol->setName($rolToUpdate->getName());
            $updatedRol->setDescription($rolToUpdate->getDescription());
            $updatedRol->setIsActive($rolToUpdate->getIsActive());
            $updatedRol->setUpdateAt(Carbon::now('utc'));
            
            $result = $this->rolRepository->updateRol($updatedRol);
            return $result;
        } catch (NotFoundException $ex){
            throw $ex;
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function addPermissionToRol(string $rolId, string $permissionId):bool{
        try{
            $permissions = $this->permissionRepository->fetchByRol($rolId);            
            $listPermissions = $permissions->where('permissionId','=',$permissionId)->all();
            
            if(count($listPermissions) > 0 ){
                throw new BadRequestException('El permiso ya se encuentra asignado al rol');
            }
            $timeUtc = Carbon::now('utc');
            $result = $this->permissionRepository->addToRol($rolId, $permissionId, $timeUtc);
            
            return $result;
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function addMenuToRol(string $rolId, string $menuId):bool{
        try{
            $rolMenus = $this->menuRepository->fetchByRol($rolId);            
            $rolMenu = $rolMenus->where('menuId','=',$menuId)->all();
            if(count($rolMenu) > 0 ){
                throw new BadRequestException('El menu ya se encuentra asignado al rol');
            }
            $timeUtc = Carbon::now('utc');
            $result = $this->menuRepository->addMenuToRol($rolId, $menuId, $timeUtc);
            
            return $result;
        } catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function removeMenuFromRol(string $rolId, string $menuId):bool{
        try{
            $result = $this->menuRepository->removeMenuFromRol($rolId, $menuId);
            return $result;
        }catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function removePermissionFromRol(string $rolId, string $permissionId):bool{
        try{
            $result = $this->permissionRepository->removeFromRol($rolId, $permissionId);
            return $result;
        }catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
    
    public function getRolSummary():array{
        try{
            // Filter for total roles (excluding deleted)
            $filterTotal = new Filter();
            $filterTotal->setAvailable(true);
            $total = $this->rolRepository->countByFilter($filterTotal);
            
            // Filter for active roles (excluding deleted)
            $filterActive = new Filter();
            $filterActive->setAvailable(true);
            $filterActive->setActive('1');
            $active = $this->rolRepository->countByFilter($filterActive);
            
            return [
                'entity' => 'Roles',
                'total' => $total,
                'active' => $active
            ];
        } catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }
}
