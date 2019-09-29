<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Template\TemplateItemCollection;
use App\Http\Schema\Template\ChecklistResource;
use App\Item;

class DetailTemplateResource extends Resource
{

    public function toArray($request)
    {
        return [
            'data'          => [
                'type'          => "templates",
                'id'            => $this->id,
                'attributes'    => [
                    'name'          => $this->name,
                    'items'         => new CreateChecklistTemplateItemCollection(Item::where('checklist_id',$this->checklist->id)->get()),
                    'checklist'     => [
                        'description'       => $this->checklist->description,
                        'due_interval'      => $this->checklist->due_interval,
                        'due_unit'          => $this->checklist->due_unit
                    ],
                ],
                'links' => [
                    'self' => route('getchecklisttemplate',['templateId' => $this->id])
                ]
            ]
        ];
    }
}