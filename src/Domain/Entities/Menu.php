<?php
namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class Menu implements Arrayable {
    private string $menuId;
    private string $name;
    private ?string $route;
    private string $icon;
    private ?string $parentId;
    private DateTime $createdAt;
    
    public function __construct() {
        $this->parentId = null;
    }


    public function getMenuId(): string {
        return $this->menuId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getRoute(): ?string {
        return $this->route;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setMenuId(string $menuId): void {
        $this->menuId = $menuId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setRoute(?string $route): void {
        $this->route = $route;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
    
    public function getParentId(): ?string {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void {
        $this->parentId = $parentId;
    }

    
    
    public function toArray() {
        return [
            "menuId"=>$this->getMenuId(),
            "name"=>$this->getName(),
            "route"=>$this->getRoute(),
            "icon"=>$this->getIcon(),
            "parentId"=>$this->getParentId(),
            "createdAt"=> $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
