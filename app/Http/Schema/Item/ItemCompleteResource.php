<?php
namespace App\Http\Schema\Item;
use Illuminate\Http\Resources\Json\Resource;

class ItemCompleteResource extends Resource
{
    
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'item_id'       => $this->id,
            'is_completed'  => $this->is_completed,
            'checklist_id'  => $this->checklist_id
        ];
    }
}