<?php

namespace UesPlay\Domain\Entities;

use DateTime;

class EmailTemplate {
    private string $templateId;
    private string $code;
    private string $content;
    private string $origin;
    private string $subject;
    private DateTime $createdAt;
    
    public function getTemplateId(): string {
        return $this->templateId;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getOrigin(): string {
        return $this->origin;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setTemplateId(string $templateId): void {
        $this->templateId = $templateId;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setOrigin(string $origin): void {
        $this->origin = $origin;
    }

    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }
    
    public function getFrom(): string {
        return $this->from;
    }

    public function setFrom(string $from): void {
        $this->from = $from;
    }
}
