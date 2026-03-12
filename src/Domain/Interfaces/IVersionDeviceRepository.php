<?php
namespace UesPlay\Domain\Interfaces;
use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\Device;
use UesPlay\Domain\Entities\Version;

interface IVersionDeviceRepository
{
    /**
     * Recupera una colección de entidades Device que estan asociados a una version.
     * 
     * @param $versionId, //Id de la version que desamos recuperar los dispositivos asociados.
     * @return Collection A collection of Device entities matching the filter.
     */
    function fetchForVersion(string $versionId, Filter $filter): Collection;
    
    /**
     * Inserta un nuevo registro de asociación entre una versión y un dispositivo.
     * 
     * @param string $versionId The ID of the version to count associated devices for.
     * @return int The count of Device entities associated with the specified version.
     */
    function insert(Version $version, Device $device):bool;
    
    /**
     * Elimina todas las asociaciones de dispositivos para una versión específica.
     * 
     * @param string $versionId The ID of the version to remove device associations for.
     * @return bool True if the operation was successful, false otherwise.
     */
    function bulkRemove(string $versionId):bool;
    
}

