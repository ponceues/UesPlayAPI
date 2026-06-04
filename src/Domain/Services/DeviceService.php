<?php

namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Interfaces\IDeviceRepository;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\Device;

class DeviceService {
    private readonly IDeviceRepository $deviceRepository;
    
    public function __construct(IDeviceRepository $deviceRepository) {
        $this->deviceRepository = $deviceRepository;
    }
    
    public function listDevices(Filter $filter):Envelop{
        try{
            $result = new Envelop();
            $filter->setActive(true);
            $filter->setAvailable(true);
            $count = $this->deviceRepository->countByFilter($filter);
            $devices = $this->deviceRepository->fetchByFilter($filter);
            
            $result->setData($devices, $filter, $count, "devices");
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function createDevice(Device $device):Device{
        try{
            $exist=$this->deviceRepository->findByName($device->getName());
            if($exist !== null){
                throw new BadRequestException('El nombre del dispositivo ya existe');
            }
            
            $device->setDeviceId(Uuid::uuid4()->toString());
            $device->setDeleted(false);
            $device->setCreatedAt(Carbon::now('utc'));
            $device->setUpdatedAt(Carbon::now('utc'));
            
            $result= $this->deviceRepository->create($device);
            
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
    
    public function fetchDevices(Filter $filter):Envelop{
        try{
            $result = new Envelop();
            $filter->setActive(true);
            $filter->setAvailable(true);
            $count = $this->deviceRepository->countByFilter($filter);
            $devices = $this->deviceRepository->fetchByFilter($filter);
            
            $result->setData($devices, $filter, $count, "devices");
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function fetchDevicesForAdmin(Filter $filter):Envelop{
        try{
            $result = new Envelop();
            $count = $this->deviceRepository->countByFilter($filter);
            $devices = $this->deviceRepository->fetchByFilter($filter);
            
            $result->setData($devices, $filter, $count, "devices");
            return $result;
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function updateDevice(Device $device):Device{
        try{
            $count=$this->deviceRepository->countForUpdate($device->getDeviceId(),$device->getName());
            if($count > 0){
                throw new BadRequestException('El nombre ya se encuentra utilizado por otro ');
            }
            
            $device->setUpdatedAt(Carbon::now('utc'));
            
            $result= $this->deviceRepository->update($device);
            
            return $result;
        }   
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function findDevice(string $deviceId):Device{
        try{
            $result= $this->deviceRepository->findById($deviceId);            
            return $result;
        }   
        catch (NotFoundException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function deleteDevice(string $deviceId):array{
        try{
            $result= $this->deviceRepository->delete($deviceId);
            return [
                'result'=>$result
            ];
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
    
    public function summaryDevices():array{
        try{
            $summary = $this->deviceRepository->getSummary();
            return [
                'entity' => 'Dispositivos',
                'total' => $summary['total'],
                'active' => $summary['active']
            ];
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado.");
        }
    }
}
