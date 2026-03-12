<?php
namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class License implements Arrayable
{ 
    private string $licenseId;
    private string $name;
    private string $version;
    private string $description;
    private bool $enabled;
    private bool $deleted;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function getLicenseId(): string {
        return $this->licenseId;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getVersion(): string {
        return $this->version;
    }
    public function getDescription(): string {
        return $this->description;
    }
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }
    public function setLicenseId(string $licenseId): void {
        $this->licenseId = $licenseId;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }
    public function setVersion(string $version): void {
        $this->version = $version;
    }
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
    public function isEnabled(): bool {
        return $this->enabled;
    }
    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }
    
    public function isDeleted(): bool {
        return $this->deleted;
    }
    
    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }
    
    public function toArray(): array {
        return [
            'licenseId' => $this->getLicenseId(),
            'name' => $this->getName(),
            'version' => $this->getVersion(),
            'description' => $this->getDescription(),
            'enabled' => $this->isEnabled(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}