<?php

namespace UesPlay\Domain\Interfaces;
use UesPlay\Domain\Entities\User;
use UesPlay\Domain\Helpers\Filter;
use DateTime;

interface IUserRepository {
    function insert(User $user):User;
    function findById(string $userId):User;
    function findByEmail(string $email):?User;
    function searchByFilter(Filter $filter);
    function countByFilter(Filter $filter):int;
    function updateUser(User $user):User;
    /**
     * <p>Agrega un usuario a la db con codigo de creacion </p>
     * @param string $user
     * @param string $code
     * @return boolean
     */
    function insertWithCode(User $user, string $code):bool;
    
    /**
     * <p>Agrega un area a la lista de areas del usuario </p>
     * @param string $UserId 
     * @param string $areaId
     * @param DateTime $createdAt
     * @return boolean
     */
    function addArea(string $userId, string $areaId,DateTime $createdAt):bool;

    /**
     * <p> Verifica la identidad del usuario en vase a verify_code e dentity. </p>
     * @param string $identity 
     * @param string $verify_code
     * @return boolean
     */
    function verfifyAccount(string $identity, string $verify_code): ?User;
    /**
     * <p> Verifica el paso quen que se encuentra la cuenta del usuario, RECOVERY, VERIFY. </p>
     * @param string $identity
     * @param string $verify_code
     * @param string $step
     * @return boolean
     */
    function verfifyAccountStep(string $identity, string $verify_code, string $step): ?User;
    
    /**
     * <p> Actualiza el password de un usuario </p>
     * @param string $password
     * @return boolean
     */
    function updatePassword(string $userId,string $password):bool;

    /**
     * <p> Completa el flujo dela creacion de cuenta </p>
     * @param string $userId
     * @param string $stateId
     * @return boolean
     */
    function completeAccount(string $userId,string $stateId, string $password):bool;
    
    /**
     * <p> Completa el flujo dela creacion de cuenta </p>.
     * @param string $userId
     * @return boolean
     */
    
    function disableAllAreas(string $userId):bool;
    /**
     * <p> Activa un area del usuario inactivo </p>.
     * @param string $userId
     * @return boolean
     */
    
    
    function activateArea(string $userId, string $areaId):bool;

    /**
     * <p> Crea un registro de condigo de verificacion temporal para reseteo de password </p>.
     * @param string $userId
     * @param string $code
     * @param string $securityCode
     * @return boolean
     */
    function updateSecurity(string $userId, string $sercurityCode, string $mode):bool;
    
    /**
     * <p> Crea un registro de condigo de verificacion temporal para reseteo de password </p>.
     * @param string $userId
     * @param string $code
     * @param string $securityCode
     * @return boolean
     */
    function getUserData(string $userId, string $sercurityCode, string $mode);
}
