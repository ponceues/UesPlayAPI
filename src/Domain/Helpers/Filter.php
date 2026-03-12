<?php
namespace UesPlay\Domain\Helpers;

class Filter {
    private int $page;
    private int $pageSize;
    private ?string $id;
    private ?string $code;
    private ?string $text;
    private ?string $name;
    private ?string $active;
    private ?bool $enabled;
    private ?bool   $available;
    private ?string $email;
    private ?string $stateId;
    private ?string $typeId;
    private ?string $platformId;
    private ?string $deviceId;
    private ?string $areaId;
    private ?string $mediaTypeId;
    private ?string $status;
    
    public function __construct() {
        $this->page = 0;
        $this->pageSize = 10;
        $this->Id = null;
        $this->text = null;
        $this->name= null;
        $this->available = null;
        $this->code = null;
        $this->active = null;
        $this->email=null;
        $this->stateId = null;
        $this->typeId = null;
        $this->platformId = null;
        $this->deviceId = null;
        $this->areaId = null;
        $this->enabled = null;
        $this->mediaTypeId = null;
        $this->status = null;
    }
    
    public function getPage(): int {
        return $this->page;
    }

    public function getPageSize(): int {
        return $this->pageSize;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setPage(int $page): void {
        $this->page = $page;
    }

    public function setPageSize(int $pageSize): void {
        $this->pageSize = $pageSize;
    }

    public function setId(?string $id): void {
        $this->id = $id;
    }

    public function setText(?string $text): void {
        $this->text = $text;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }
    
    public function getAvailable(): ?bool {
        return $this->available;
    }
    public function getEnabled(): ?bool {
        return $this->enabled;
    }
    public function setEnabled(?bool $enabled): void {
        $this->enabled = $enabled;
    }

    public function setAvailable(?bool $available): void {
        $this->available = $available;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(?string $code): void {
        $this->code = $code;
    }

    public function getActive(): ?string {
        return $this->active;
    }

    public function setActive(?string $active): void {
        $this->active = $active;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }
    
    public function getStateId(): ?string {
        return $this->stateId;
    }

    public function setStateId(?string $stateId): void {
        $this->stateId = $stateId;
    }
    
    public function getTypeId(): ?string {
        return $this->typeId;
    }

    public function setTypeId(?string $typeId): void {
        $this->typeId = $typeId;
    }
    
    public function getPlatformId(): ?string {
        return $this->platformId;
    }
    
    public function setPlatformId(?string $platformId): void {
        $this->platformId = $platformId;
    }
    
    public function getDeviceId(): ?string {
        return $this->deviceId;
    }
    
    public function setDeviceId(?string $deviceId): void {
        $this->deviceId = $deviceId;
    }
    
    public function getAreaId(): ?string {
        return $this->areaId;
    }
    
    public function setAreaId(?string $areaId): void {
        $this->areaId = $areaId;
    }
    
    public function getMediaTypeId(): ?string {
        return $this->mediaTypeId;
    }
    
    public function setMediaTypeId(?string $mediaTypeId): void {
        $this->mediaTypeId = $mediaTypeId;
    }
    
    public function getStatus(): ?string {
        return $this->status;
    }
    
    public function setStatus(?string $status): void {
        $this->status = $status;
    }
}
