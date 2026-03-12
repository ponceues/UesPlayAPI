<?php
namespace UesPlay\Domain\Entities;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class MediaType implements Arrayable
{
    private string $typeId;
    private string $name;
    private string $description;
    private bool $enabled;
    private bool $deleted;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    // Getters
    public function getTypeId(): string
    {
        return $this->typeId;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    public function isDeleted(): bool
    {
        return $this->deleted;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    // Setters
    public function setTypeId(string $typeId): void
    {
        $this->typeId = $typeId;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'typeId' => $this->typeId,
            'name' => $this->name,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'createdAt' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}