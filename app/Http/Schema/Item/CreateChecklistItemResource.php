<?php
namespace App\Http\Schema\Item;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Item\ItemCollection;

class CreateChecklistItemResource extends Resource
{
    
    public function toArray($request)
    {
        return [
                'data'          => [
                    'type'          => 'checklists',
                    'id'            => $this->id,
                    'attributes'    => [
                        'description'       => $this->description,
                        'is_completed'      => $this->is_completed,
                        'completed_at'      => $this->completed_at,
                        'due'               => $this->due,
                        'urgency'           => $this->urgency,
                        'updated_by'        => $this->updated_by,
                        'update_at'         => $this->updated_at,
                        'created_at'        => $this->created_at
                    ],
                    'links' => [
                        'self' => route('getchecklist',['checklistId' => $this->id])
                    ]    
                ]
        ];
    }
}