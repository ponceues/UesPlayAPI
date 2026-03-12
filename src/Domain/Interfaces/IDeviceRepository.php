<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;

use UesPlay\Domain\Entities\Device;
use UesPlay\Domain\Helpers\Filter;

interface IDeviceRepository {
    /**
     * <p>Crea un nuevo dispositiv y retorna el objeto creado </p>
     * 
     * @param Device $device
     * @return Device
     */
    function create(Device $device):Device;
    
    /**
     * <p> Recupera el conteo de dispositivos que concuerden con el dato  </p>
     * @param Filter $filter
     * @return int
     */
    function countByFilter(Filter $filter):int;
    
    /**
     * <p> Recupera los dispositivos que concuerden con los filtros </p>
     * @param Filter $filter
     * @return Collection
     */
    
    function countForUpdate(string $deviceId, string $name):int;
    
    /**
     * <p> Recupera los dispositivos que concuerden con los filtros </p>
     * @param Filter $filter
     * @return Collection
     */
        
    function fetchByFilter(Filter $filter):Collection;
    
    /**
     * <p> Recupera el dispositivo filtrado por Id  </p>
     * @param string $id
     * @return Device
     */
    function findById(string $id):Device;
    
    /** 
     * <p>Busca un dispositivo o retorna null si no lo encuentra </p>
     * @param string $id
     * @return Device|null
     */
    function findOrDefault(string $id):?Device;
    
    /**
     * <p>Recupera el device por nombre o retorna null si no lo encuentra </p>
     * 
     * @param string $name
     * @return Device|null
     */
    function findByName(string $name):?Device;
    
    /**
     * <p> Actualiza un dispositivo y retonra el objeto DB actualizado </p>
     * @param Device $device
     * @return Device
     */
    function update(Device $device):Device;
    
    /**
     * <p> Ejecuta eliminacion logica de un dispositivo. </p>
     * @param Device $device
     * @return bool
     */
    function delete(string $deviceId):bool;
    
}
