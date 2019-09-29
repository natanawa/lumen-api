<?php
namespace App\Http\Schema\Item;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Item\ItemResource;

class ItemCollection extends ResourceCollection
{
    
    public function toArray($request)
    {
        return $this->collection->transform(function ($item){
            return new ItemResource($item);
        });
    }
}