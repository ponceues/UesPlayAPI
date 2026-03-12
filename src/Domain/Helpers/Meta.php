<?php
namespace UesPlay\Domain\Helpers;
use Illuminate\Contracts\Support\Arrayable;


class Meta implements Arrayable{
    private int $count;
    private int $pageSize;
    private int $page;
    private string $key;
    
    public function getCount(): int {
        return $this->count;
    }

    public function getPageSize(): int {
        return $this->pageSize;
    }

    public function getPage(): int {
        return $this->page;
    }

    public function getKey(): string {
        return $this->key;
    }

    public function setCount(int $count): void {
        $this->count = $count;
    }

    public function setPageSize(int $pageSize): void {
        $this->pageSize = $pageSize;
    }

    public function setPage(int $page): void {
        $this->page = $page;
    }

    public function setKey(string $key): void {
        $this->key = $key;
    }

    public function toArray(): array{
        return [
            "count"=>$this->getCount(),
            "page"=>$this->getPage(),
            "pageSize"=>$this->getPageSize(),
            "key"=>$this->getKey()
        ];
    }
}
