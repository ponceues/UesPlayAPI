<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;

interface IVersionPlatformRepository
{
    /**
     * Recupera una colección de entidades Platform que estan asociados a una version.
     * 
     * @param Filter $filter The filter criteria for fetching platforms.
     * @return Collection A collection of Platform entities matching the filter.
     */
    function fetchByVersion(string $versionId, Filter $filter): Collection;
    
    /**
     * Inserta un nuevo registro de asociación entre una versión y una plataforma.
     * 
     * @param string $versionId The ID of the version to count associated platforms for.
     * @return int The count of Platform entities associated with the specified version.
     */
    function insert(string $versionId, string $platformId):bool;
    
    /**
     * Elimina todas las asociaciones de plataformas para una versión específica.
     * 
     * @param string $versionId The ID of the version to remove platform associations for.
     * @return bool True if the operation was successful, false otherwise.
     */
    function bulkRemove(string $versionId):bool;
}