<?php

namespace UesPlay\Domain\Helpers;

use Illuminate\Support\Collection;

class Envelop {
    
    private Meta $meta;
    private Collection $data;

    public function getMeta(): Meta{
        return $this->meta;
    }
    
    public function setData(Collection $data,Filter $filter,int $count,string $key): void{
        $meta = new Meta();
        $meta->setKey($key);
        $meta->setPage($filter->getPage());
        $meta->setPageSize($filter->getPageSize());
        $meta->setCount($count);

        $this->meta=$meta;
        $this->data = $data;
    }

    public function  toArray():array{
        return [
            "meta"=>$this->getMeta()->toArray(),
            $this->getMeta()->getKey()=>$this->data->toArray()
        ];
    }
    
}
