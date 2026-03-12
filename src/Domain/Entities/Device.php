<?php

namespace UesPlay\Domain\Entities;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class Device implements Arrayable {
    private string $deviceId;
    private string $name;
    private ?string $description;
    private bool    $active;
    private bool    $deleted;
    private string  $icon;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function getDeviceId(): string {
        return $this->deviceId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function getDeleted(): bool {
        return $this->deleted;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setDeviceId(string $deviceId): void {
        $this->deviceId = $deviceId;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
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
    
    public function getIcon(): string {
        return $this->icon;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

        
    public function toArray() {
        return [
            'deviceId'=>$this->getDeviceId(),
            'name'=>$this->getName(),
            'description'=>$this->getDescription(),
            'active'=>$this->getActive(),
            'icon'=>$this->getIcon(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
