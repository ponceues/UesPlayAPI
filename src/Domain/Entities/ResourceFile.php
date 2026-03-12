<?php
namespace UesPlay\Domain\Entities;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class ResourceFile implements Arrayable
{
    private string $fileId;
    private string $resourceId;
    private string $name;
    private string $extension;
    private bool $deleted;
    private string $path;
    private ?string $url;
    private DateTime $createdAt;
    private string $option;
    private string $type;
    
    
    public function __construct()
    {
        $this->url = null;
    }
     
    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function setFileId(string $fileId): void
    {
        $this->fileId = $fileId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setResourceId(string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }
    
    public function getResourceId(): string
    {
        return $this->resourceId;
    }
    
    public function setPath(string $path): void
    {
        $this->path = $path;
    }
    
    public function getPath(): string
    {
        return $this->path;
    }
    
   
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
    
    
    public function getUrl(): ?string
    {
        return $this->url;
    }

    
    public function setOption(string $option): void
    {
        $this->option = $option;
    }
    
    public function getOption(): string
    {
        return $this->option;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function toArray(): array
    {
        return [
            'fileId' => $this->getFileId(),
            'resourceId' => $this->getResourceId(),
            'name' => $this->getName(),
            'extension' => $this->getExtension(),
            'url' =>$this->getUrl() === null ? null : $this->getUrl(),
            'option' => $this->getOption(),
            'type' => $this->getType(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}