<?php

namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class ResourceType implements Arrayable {
    private string $typeId;
    private string $name;
    private string $code;
    private bool $active;
    private bool $deleted;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function getTypeId(): string {
        return $this->typeId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setTypeId(string $typeId): void {
        $this->typeId = $typeId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
    
    public function getActive(): bool {
        return $this->active;
    }

    public function getDeleted(): bool {
        return $this->deleted;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }
    
    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

            
    public function toArray():array {
        return [
            'typeId'=>$this->getTypeId(),
            'code'=>$this->getCode(),
            'name'=>$this->getName(),
            'active'=>$this->getActive(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
