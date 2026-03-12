<?php
namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use DateTime;

class Version implements Arrayable
{
    private string $versionId;
    private string $resourceId;
    private string $description;
    private string $version;
    private string $sourceUrl;
    private string $fileName;
    private string $licenseId;
    private DateTime $createdAt;

    private ?License $license;
    private Collection $languages;
    private Collection $platforms;
    private Collection $devices;

    
    public function __construct()
    {
        $this->langs = collect();
        $this->platforms = collect();
        $this->devices = collect();
        $this->license = null;
    }

    public function getVersionId(): string
    {
        return $this->versionId;
    }

    public function setVersionId(string $versionId): void
    {
        $this->versionId = $versionId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): void
    {
        $this->sourceUrl = $sourceUrl;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getLanguages(): Collection
    {
        return $this->langs;
    }

    public function setLanguages(Collection $languages): void
    {
        $this->langs = $languages;
    }

    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function setDevices(Collection $devices): void
    {
        $this->devices = $devices;
    }

    public function getPlatforms(): Collection
    {
        return $this->platforms;
    }

    public function setPlatforms(Collection $platforms): void
    {
        $this->platforms = $platforms;
    }
    
    public function getFileName(): string
    {
        return $this->fileName;
    }
    
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }
    
    public function setLicense(?License $license): void
    {
        $this->license = $license;
    }

    public function getLicenseId(): string
    {
        return $this->licenseId;
    }
    
    public function setLicenseId(string $licenseId): void
    {
        $this->licenseId = $licenseId;
    }
    
    
    
    public function toArray(): array
    {
        return [
            'versionId' => $this->getVersionId(),
            'resourceId' => $this->getResourceId(),
            'description' => $this->getDescription(),
            'version' => $this->getVersion(),
            'fileName' => $this->getFileName(),
            'licenceId' => $this->getLicenseId(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'langs' => $this->langs->toArray(),
            'platforms' => $this->platforms->toArray(),
            'devices' => $this->devices->toArray(),
            'license' => $this->getLicense()?->toArray(),
        ];
    }
}