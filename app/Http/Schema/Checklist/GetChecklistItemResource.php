<?php
namespace App\Http\Schema\Checklist;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Item\ItemCollection;

class GetChecklistItemResource extends Resource
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
                    'created_by'        => $this->created_by,
                    'checklist_id'      => $this->checklist_id,
                    'assignee_id'       => $this->assignee_id,
                    'task_id'           => $this->task_id,
                    'deleted_at'        => $this->deleted_at,
                    'created_at'        => $this->created_at,
                    'updated_at'        => $this->updated_at
                ],
                'links' => [
                    'self' => route('getchecklist',['checklistId' => $this->checklist_id]),
                ],
            ]
        ];
    }
}