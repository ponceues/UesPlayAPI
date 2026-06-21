<?php

namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use DateTime;

class Resource implements Arrayable {
    private string $resourceId;
    private string $userId;
    private string $typeId;
    private ?string $areaId;
    private ?string $mediaTypeId;
    private ?string $genreId;
    private string $stateId;
    private string $title;
    private string $description;
    private int $downloads;   
    private float $rating;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private Collection $files;

    private ?User $creator;
    private ?ResourceState $state;
    private ?Area $area;
    private ?ResourceType $type;
    private Collection $authors;
    private ?User $user;
    private ?Version $version;
    


    /**
     * @return ?string
     */
    public function getMediaTypeId(): ?string
    {
        return $this->mediaTypeId;
    }

    /**
     * @return ?string
     */
    public function getGenreId(): ?string
    {
        return $this->genreId;
    }

    /**
     * @param ?string $mediaTypeId
     */
    public function setMediaTypeId(?string $mediaTypeId): self
    {
        $this->mediaTypeId = $mediaTypeId;
        
        return $this;
    }

    /**
     * @param ?string $genreId
     */
    public function setGenreId(?string $genreId): self
    {
        $this->genreId = $genreId;
        
        return $this;
    }

    public function __construct() {
        $this->dowloadsCount = 0;
        $this->creator = null;
        $this->area=null;
        $this->state = null;
        $this->type = null;
        $this->authors = collect();
        $this->user = null;
        $this->files = collect();
        $this->version = null;
        $this->rating = 0.0;
        $this->genreId = null;
        $this->mediaTypeId = null;
    }
    
    
    
    public function getResourceId(): string {
        return $this->resourceId;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function getTypeId(): string {
        return $this->typeId;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDownloads(): int {
        return $this->downloads;
    }

    public function setDownloads(int $dowloads): void {
        $this->downloads = $dowloads;
    }

    
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function getCreator(): ?User {
        return $this->creator;
    }

    public function getState(): ?ResourceState {
        return $this->state;
    }

    public function getArea(): ?Area {
        return $this->area;
    }

    public function getType(): ?ResourceType {
        return $this->type;
    }

    public function getAuthors(): Collection {
        return $this->authors;
    }

    public function setResourceId(string $resourceId): void {
        $this->resourceId = $resourceId;
    }

    public function setUserId(string $userId): void {
        $this->userId = $userId;
    }


    public function setTypeId(string $typeId): void {
        $this->typeId = $typeId;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
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

    public function setCreator(?User $creator): void {
        $this->creator = $creator;
    }

    public function setState(?ResourceState $state): void {
        $this->state = $state;
    }

    public function setArea(?Area $area): void {
        $this->area = $area;
    }

    public function setType(?ResourceType $type): void {
        $this->type = $type;
    }

    public function setAuthors(Collection $authors): void {
        $this->authors = $authors;
    }
    
    public function getStateId(): string {
        return $this->stateId;
    }

    public function setStateId(string $stateId): void {
        $this->stateId = $stateId;
    }
    
    public function getAreaId(): ?string {
        return $this->areaId;
    }

    public function setAreaId(?string $areaId): void {
        $this->areaId = $areaId;
    }
    
    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }
    
    public function getFiles(): Collection {
        return $this->files;
    }
    
    public function setFiles(Collection $files): void {
        $this->files = $files;
    }
    
    public function getVersion(): ?Version {
     
        return $this->version;
    }

    public function setVersion(?Version $version): void {
        $this->version = $version;
    }
    
    public function getRating(): float
    {
        return $this->rating;
    }
    
    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        
        return $this;
    }
    
    
    public function toArray(): array {
        return [
            'resourceId'=>$this->getResourceId(),
            'userId'=>$this->getUserId(),            
            'typeId'=>$this->getTypeId(),
            'areaId'=>$this->getAreaId(),
            'stateId'=>$this->getStateId(),
            'mediaTypeId'=> $this->getMediaTypeId(),
            'genreId'=>$this->getGenreId(),
            'title'=>$this->getTitle(),
            'description'=>$this->getDescription(),
            'downloads'=>$this->getDownloads(),
            'user'=>$this->getUser()?->toArray(),
            'area'=>$this->getArea()?->toArray(),
            'type'=>$this->getType()?->toArray(),
            'state'=>$this->getState()?->toArray(),
            'authors'=>$this->getAuthors()->toArray(),
            'files'=>$this->getFiles()->toArray(),
            'rating'=>$this->getRating(),
            'version'=>$this->getVersion() == null ? null : $this->getVersion()->toArray(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt'=>$this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }


}
