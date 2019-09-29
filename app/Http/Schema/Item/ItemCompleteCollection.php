<?php
namespace App\Http\Schema\Item;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Item\ItemCompleteResource;

class ItemCompleteCollection extends ResourceCollection
{
    
    public function toArray($request)
    {
        return [
            'data'  => $this->collection->transform(function ($item){
                return new ItemCompleteResource($item);
            })
        ];
    }
}