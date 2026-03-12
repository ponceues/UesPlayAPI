<?php
namespace UesPlay\Domain\Entities;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;

class Comment implements Arrayable
{
    private string $commentId;
    private string $resourceId;
    private string $comment;
    private int $score;
    private string $status;
    private string $commentedBy;
    private string $commentersEmail;
    private bool $deleted;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    
    public function getCommentId(): string {
        return $this->commentId;
    }

    public function setCommentId(string $commentId): void {
        $this->commentId = $commentId;
    }

    public function getResourceId(): string {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): void {
        $this->resourceId = $resourceId;
    }

    public function getComment(): string {
        return $this->comment;
    }

    public function setComment(string $comment): void {
        $this->comment = $comment;
    }

    public function getScore(): int {
        return $this->score;
    }

    public function setScore(int $score): void {
        $this->score = $score;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function getCommentedBy(): string {
        return $this->commentedBy;
    }

    public function setCommentedBy(string $commentedBy): void {
        $this->commentedBy = $commentedBy;
    }

    public function getCommentersEmail(): string {
        return $this->commentersEmail;
    }

    public function setCommentersEmail(string $commentersEmail): void {
        $this->commentersEmail = $commentersEmail;
    }

    public function isDeleted(): bool {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array {
        return [
            'commentId' => $this->getCommentId(),
            'resourceId' => $this->getResourceId(),
            'comment' => $this->getComment(),
            'score' => $this->getScore(),
            'status' => $this->getStatus(),
            'commentedBy' => $this->getCommentedBy(),
            'commentersEmail' => $this->getCommentersEmail(),
            'deleted' => $this->isDeleted(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}