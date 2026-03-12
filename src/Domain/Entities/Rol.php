<?php
namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use DateTime;

class Rol implements Arrayable {
    private string $rolId;
    private string $name;
    private string $description;
    private bool $isDefault;
    private bool $isActive;
    private bool $isDelete;
    private Collection $permissions;
    private Collection $menus;
    private DateTime $createdAt;
    private DateTime $updateAt;
    
    public function __construct() {
        $this->permissions = collect([]);
        $this->menus = collect();
    }
    
    public function getRolId(): string {
        return $this->rolId;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setRolId(string $rolId): void {
        $this->rolId = $rolId;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdateAt(): DateTime {
        return $this->updateAt;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdateAt(DateTime $updateAt): void {
        $this->updateAt = $updateAt;
    }
    
    public function getPermissions(): Collection {
        return $this->permissions;
    }

    public function getMenus(): Collection {
        return $this->menus;
    }

    public function setPermissions(Collection $permissions): void {
        $this->permissions = $permissions;
    }

    public function setMenus(Collection $menus): void {
        $this->menus = $menus;
    }
    
    public function getIsDefault(): bool {
        return $this->isDefault;
    }

    public function getIsActive(): bool {
        return $this->isActive;
    }

    public function getIsDelete(): bool {
        return $this->isDelete;
    }

    public function setIsDefault(bool $isDefault): void {
        $this->isDefault = $isDefault;
    }

    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    public function setIsDelete(bool $isDelete): void {
        $this->isDelete = $isDelete;
    }

    
    public function toArray() {
        return [
            "rolId"=>$this->getRolId(),
            "name"=>$this->getName(),
            "description"=>$this->getDescription(),
            'isActive'=>$this->getIsActive(),
            'isDefault'=>$this->getIsDefault(),
            "menus"=>$this->getMenus()->toArray(),
            "permissions"=>$this->getPermissions()->toArray(),
            "createdAt"=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            "updatedAt"=>$this->getUpdateAt()->format('Y-m-d H:i:s')
        ];
    }
}
