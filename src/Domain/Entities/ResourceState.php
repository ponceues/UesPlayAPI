<?php

namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class ResourceState implements Arrayable {
    private string $stateId;
    private string $code;
    private string $name;
    private string $description;
    private DateTime $createdAt;
    
    public function getStateId(): string {
        return $this->stateId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setStateId(string $stateId): void {
        $this->stateId = $stateId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
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

            
    public function toArray(): array {
        return [
            'stateId'=>$this->getStateId(),
            'code'=>$this->getCode(),
            'name'=>$this->getName()
        ];
    }
}
