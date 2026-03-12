<?php

namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class Area implements Arrayable{
    private string $areaId;
    private string $code;
    private string $name;
    private bool $active;
    private bool $deleted;
    private ?string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function getAreaId(): string {
        return $this->areaId;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setAreaId(string $areaId): void {
        $this->areaId = $areaId;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
    
    public function getDeleted(): bool {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }
    
    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

            
    public function toArray(): array {
        return [
            'areaId'=>$this->getAreaId(),
            'code'=>$this->getCode(),
            'name'=>$this->getName(),
            'active'=>$this->getActive(),
            'description'=>$this->getDescription(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt'=>$this->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
