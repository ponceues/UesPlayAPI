<?php

namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class UserState implements Arrayable {
    private string $stateId;
    private string $code;
    private string $name;
    private DateTime $createdAt;
    
    public function getStateId(): string {
        return $this->stateId;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setStateId(string $stateId): void {
        $this->stateId = $stateId;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function toArray() {
        return [
            "stateId"=>$this->getStateId(),
            "code"=>$this->getCode(),
            "name"=>$this->getName()
        ];
    }
}
