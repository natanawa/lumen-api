<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Checklist\ListofItemInGivenChecklistResource;
use App\Item;

class GetAssignTemplateResource extends Resource
{

    public function toArray($request)
    {
        return [
            'type'          => 'checklists',
            'id'            => $this->id,
            'attributes'    => [
                'object_domain'     => $this->object_domain,
                'object_id'         => $this->object_id,
                'description'       => $this->description,
                'is_completed'      => $this->is_completed,
                'due'               => $this->due,
                'urgency'           => $this->urgency,
                'completed_at'      => $this->completed_at,
                'updated_by'        => $this->updated_by,
                'created_by'        => $this->created_by,
                'update_at'         => $this->updated_at,
                'created_at'        => $this->created_at,
            ],
            'links' => [
                'self' => route('getchecklist',['checklistId' => $this->id])
            ],
            'relationships' => [
                'items' => [
                    'links' => [
                        'related' => route('createchecklistitem',['checklistId' => $this->id])
                    ],
                ]
            ]
        ];
    }
}