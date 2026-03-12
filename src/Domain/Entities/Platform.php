<?php

namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class Platform implements Arrayable {
    private string $platformId;
    private string $name;
    private string $icon;
    private string $description;
    private bool $available;
    private DateTime $createdAt;
    private DateTime $updatedAt;



    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
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
    
    public function getPlatformId(): string {
        return $this->platformId;
    }

    public function setPlatformId(string $platformId): void {
        $this->platformId = $platformId;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }
    


    public function getAvailable(): bool {
        return $this->available;
    }

    public function setAvailable(bool $available): void {
        $this->available = $available;
    }
    
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    
    public function toArray() {
        return [
            'platformId'=>$this->getPlatformId(),
            'name'=>$this->getName(),
            'description'=>$this->getDescription(),
            'icon'=>$this->getIcon(),
            'available'=>$this->getAvailable(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt'=>$this->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
