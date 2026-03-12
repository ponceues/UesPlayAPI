<?php
namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\Language;
use UesPlay\Domain\Entities\Version;

interface IVersionLangsRepository
{
    /**
     * Recupera una colección de entidades Language que estan asociados a una version.
     * 
     * @param Filter $filter The filter criteria for fetching languages.
     * @return Collection A collection of Language entities matching the filter.
     */
    function fetchByVersion(string $versionId, Filter $filter): Collection;

    /**
     * Inserta un nuevo registro de asociación entre una versión y un lenguaje.
     * 
     * @param string $versionId The ID of the version to count associated languages for.
     * @return int The count of Language entities associated with the specified version.
     */
    function insert(Version $version, Language $language):bool;
    
    /**
     * Elimina todas las asociaciones de lenguajes para una versión específica.
     * 
     * @param string $versionId elimina todas las asociaciones de lenguajes para una versión específica.
     * @return bool True si la operación fue exitosa, false de lo contrario.
     */
    function removeForVersion(string $versionId):bool;
    
}

