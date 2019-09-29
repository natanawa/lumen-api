<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Template\CreateChecklistTemplateItemCollection;
use App\Item;

class CreateChecklistTemplateResource extends Resource
{
 
    public function toArray($request)
    {
        return [
            'data'          => [
                'id'            => $this->id,
                'attributes'    => [
                    'name'          => $this->name,
                    'checklist'     => [
                        'description'       => $this->checklist->description,
                        'due_interval'      => $this->checklist->due_interval,
                        'due_unit'          => $this->checklist->due_unit
                    ],
                    'items'         => new CreateChecklistTemplateItemCollection(Item::where('checklist_id',$this->checklist->id)->get())
                ],
            ]
        ];
    }
}