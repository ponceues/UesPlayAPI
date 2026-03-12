<?php

namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use DateTime;

class User implements Arrayable {
    private string $userId;
    private string $stateId;
    private string $rolId;
    private string $name;
    private string $email;
    private ?string $password;
    private ?string $areaId;
    private array $areasCodes;
    
    private ?UserState $state;
    private ?DateTime $emailVerifiedAt;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    private Collection $areas;
    
    public function __construct() {
        $this->state = null;
        $this->areas = collect();
    }

    
    public function getUserId(): string {
        return $this->userId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getStateId(): string {
        return $this->stateId;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUserId(string $userId): void {
        $this->userId = $userId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setStateId(string $stateId): void {
        $this->stateId = $stateId;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
    
    public function getEmailVerifiedAt(): ?DateTime {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?DateTime $emailVerifiedAt): void {
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }
    public function getState(): ?UserState {
        return $this->state;
    }

    public function setState(?UserState $state): void {
        $this->state = $state;
    }
    
    public function getAreaId(): ?string {
        return $this->areaId;
    }

    public function setAreaId(?string $areaId): void {
        $this->areaId = $areaId;
    }
    
    public function getRolId(): string {
        return $this->rolId;
    }

    public function getAreas(): Collection {
        return $this->areas;
    }

    public function setRolId(string $rolId): void {
        $this->rolId = $rolId;
    }

    public function setAreas(Collection $areas): void {
        $this->areas = $areas;
    }
    
    public function getAreasCode(): array {
        return $this->areasCode;
    }
    
    public function setAreasCode(array $areasCode): void {
        $this->areasCode = $areasCode;
    }
    
    public function getAreasCodes(): array {
        return $this->areasCodes;
    }
    
    public function setAreasCodes(array $areasCodes): void {
        $this->areasCodes = $areasCodes;
    }    
        
    public function toArray() {
        return [
            "userId"=> $this->getUserId(),
            "rolId"=> $this->getRolId(),
            "stateId"=>$this->getStateId(),
            "name"=>$this->getName(),
            "email"=>$this->getEmail(),
            "verifiedAt"=>$this->getEmailVerifiedAt() === null ? null:$this->getEmailVerifiedAt()->format('Y-m-d H:i:s'),
            "createdAt"=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            "updatedAt"=>$this->getUpdatedAt()->format('Y-m-d H:i:s'),
            "state"=>$this->getState() === null ? null:$this->getState()->toArray(),
            "areas"=>$this->getAreas()->map(function(Area $area){
                return $area->toArray();
            })->toArray(),
        ];
    }
}
