<?php
namespace UesPlay\Domain\Interfaces;
use Illuminate\Support\Collection;

use UesPlay\Domain\Entities\Language;
use UesPlay\Domain\Helpers\Filter;

interface ILanguageRepository
{
    /**
     * Eliminacion logica de un lenguaje
     * @param string $languageId
     * @return bool
     */
    function delete(string $languageId): bool;
    
    /**
     * Cuenta la cantidad de lenguajes que cumplen con el filtro
     * @param Filter $filter
     * @return int
     */
    function count(Filter $filter): int;
    
    /**
     * Inserta un nuevo lenguaje
     * @param Language $language
     * @return Language
     */    
    function insert(Language $language): Language;

    /**
     * Obtiene una coleccion de lenguajes que cumplen con el filtro
     * @param Filter $filter
     * @return Collection
     */
    function fetch(Filter $filter): Collection;
    
    /**
     * Busca un lenguaje por su ID
     * @param string $languageId
     * @return Language
     */
    function find(string $languageId): Language;
    
    /**
     * Busca un lenguaje por su ID o lanza una excepcion si no lo encuentra
     * @param string $languageId
     * @return Language
     */
    function findOrDefault(string $languageId): ?Language;
    
    /**
     * Actualiza un lenguaje
     * @param Language $language
     * @return Language
     */
    function update(Language $language): Language;
}