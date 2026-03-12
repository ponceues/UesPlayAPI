<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\Platform;
use UesPlay\Domain\Helpers\Filter;

interface IPlatformRepository {
    function fetch(Filter $filter):Collection;
    function count(Filter $filter):int;
    /**
     *  <b> Busca un usuario o levanta un usuario o levanta una exception  </b>
     * @param string $id
     * @return Platform
     */
    function find(string $id):Platform;
    
    /**
     * <b> Busca un usuario por nombre o devuelve null si nolo encuentra </p>
     * @param string $id
     * @return Platform|null
     */
    function findByName(string $name):?Platform;

    /**
     * <b> Cuenta los usuario que poseen el nombre pero es diferente ala plataforma con el Id especificado <b/>
     * @param string $platformId
     * @param string $name
     * @return int
     */
    function countForUpdate(string $platformId, string $name):int;
    
    /**
     * <b> Actualizacion de una plataforma </b>
     * @param Platform $entity
     * @return Platform
     */
    function update(Platform $entity):Platform;
    /**
     * <b> Creacion de una Plataforma </b>
     * @param Platform $entity
     * @return Platform
     */
    function create(Platform $entity):Platform;
    
    /**
     * <b> Eliminacion de una plataforma </b>
     * @param $platformId
     * @return bool
     */
    function delete(string $platformId): bool;
    
}
