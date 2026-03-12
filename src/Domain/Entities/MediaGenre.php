<?php
namespace UesPlay\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;
use DateTime;

class MediaGenre implements Arrayable
{
    private string $genreId;
    private string $mediaTypeId;
    private string $name;
    private bool $enabled;
    private string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    // Getters
    public function getGenreId(): string
    {
        return $this->genreId;
    }

    public function getMediaTypeId(): string
    {
        return $this->mediaTypeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getDescription(): string
    {
        return $this->description;
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
    public function setGenreId(string $genreId): void
    {
        $this->genreId = $genreId;
    }

    public function setMediaTypeId(string $mediaId): void
    {
        $this->mediaTypeId = $mediaId;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // Método para convertir la entidad a array
    public function toArray(): array
    {
        return [
            'genreId' => $this->getGenreId(),
            'mediaTypeId' => $this->getMediaTypeId(),   
            'name' => $this->getName(),
            'enabled' => $this->isEnabled(),
            'description' => $this->getDescription(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}