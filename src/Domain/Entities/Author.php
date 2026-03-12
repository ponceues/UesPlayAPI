<?php

namespace UesPlay\Domain\Entities;
use Illuminate\Contracts\Support\Arrayable;
use DateTime;


class Author implements Arrayable {
    private string $authorId;
    private string $resourceId;
    private string $firstName;
    private string $lastName;
    private string $email;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function setResourceId(string $resourceId): void {
        $this->resourceId = $resourceId;
    }

    public function getResourceId(): string {
        return $this->resourceId;
    }
    
    public function getAuthorId(): string {
        return $this->authorId;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setAuthorId(string $authorId): void {
        $this->authorId = $authorId;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function toArray():array {
        return [
            'authorId'=>$this->getAuthorId(),
            'firstName'=>$this->getFirstName(),
            'lastName'=>$this->getLastName(),
            'email'=>$this->getEmail(),
            'createdAt'=>$this->getCreatedAt()->format('Y-m-d H:i:s')            
        ];
    }
}
