<?php
namespace App\Http\Schema\Item;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Checklist\ListofAttributeChecklistResource;

class ItemAllResource extends Resource
{
   
    public function toArray($request)
    {
        return [
            'type'          => 'items',
            'id'            => $this->id,
            'attributes'    => [
                'description'       => $this->description,
                'is_completed'      => $this->is_completed,
                'completed_at'      => $this->completed_at,
                'completed_by'      => $this->completed_by,
                'due'               => $this->due,
                'urgency'           => $this->urgency,
                'updated_by'        => $this->updated_by,
                'checklist_id'      => $this->checklist_id,
                'assignee_id'       => $this->assignee_id,
                'task_id'           => $this->task_id,
                'deleted_at'        => $this->deleted_at,
                'update_at'         => $this->updated_at,
                'created_at'        => $this->created_at,
            ],
            'links' => [
                'self' => route('detailitems',['itemId' => $this->id])
            ]
        ];
    }
}