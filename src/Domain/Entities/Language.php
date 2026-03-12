<?php
namespace UesPlay\Domain\Entities;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class Language implements Arrayable
{
    private string $languageId;
    private string $code;
    private string $name;
    private DateTime $createdAt;

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'languageId' => $this->getLanguageId(),
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
    
}