<?php
namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class Permission implements Arrayable {  
    private  string $permissionId;
    private string $code;
    private string $name;
    private string $menuId;
    private DateTime $createdAt;
    
    public function getPermissionId(): string {
        return $this->permissionId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getMenuId(): string {
        return $this->menuId;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setPermissionId(string $permissionId): void {
        $this->permissionId = $permissionId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setMenuId(string $menuId): void {
        $this->menuId = $menuId;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function toArray() {
        return [
            "permissionId"=>$this->getPermissionId(),
            "code"=>$this->getCode(),
            "name"=>$this->getName(),
            "menuId"=>$this->getMenuId(),
            "createdAt"=>$this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
