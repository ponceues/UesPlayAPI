<?php

namespace UesPlay\Domain\Interfaces;

use Illuminate\Support\Collection;
use UesPlay\Domain\Entities\Resource;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Exceptions\NotFoundException;


interface IResourceRepository {
    /**
     * <b>Metodo retorna el conteo de los recursos que concuerden con los fitros </b>
     * @param Filter $filter
     * @return int
     */
    function countByFilter(Filter $filter): int ;
    /**
     *  <b>Metodo que realiza el conteo de las entidades para su actualizacion</b>
     * @param Resource $resource
     * @return int
     */
    function countForUpdate(Resource $resource):int;
    /**
     * <b>Metodo para seleccionar los recursos filtrados</b>
     * 
     * @param Filter $filter
     * @return Collection
     */
    
    function fetchByFilter(Filter $filter):Collection;
    /**
     * <b>Metodo que busca un recurso por su Id</b>
     * @param string $resourceId
     * @return Resource
     * @throws NotFoundException
     */
    function findOrFail(string $resourceId):Resource;
    /**
     * <b>Metodo que busca un recurso por su titulo, retorna null encaso no encontrado.</b>
     * @param string $title
     * @return Resource|null
     */
    function findByTitle(string $title):?Resource;
    /**
     * <b>Metodo que registra un recurso en la base de datos</b>
     * @param Resource $resource
     * @return Resource
     */
    function insert(Resource $resource):Resource;
    /**
     * <b> Metodo que realiza la actualizacion de un recurso</b>
     * 
     * @param Resource $resource
     * @return Resource
     */
    function update(Resource $resource):Resource;
    /**
     * <b>Metodo que realiza la eliminacion logica del recurso en base de datos</b>
     * @param Resource $resource
     * @return bool
     */
    function delete(Resource $resource):bool;

    /**
     * <b>Metodo que actualiza el estado del recurso</b>
     * @param Resource $resource
     * @return Resource
     */
    function updateState(Resource $resource):Resource;
}
