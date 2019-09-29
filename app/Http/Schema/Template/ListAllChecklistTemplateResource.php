<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Schema\Template\TemplateItemCollection;
use App\Http\Schema\Template\ChecklistResource;
use App\Item;

class ListAllChecklistTemplateResource extends Resource
{
   
    public function toArray($request)
    {
        if(isset($this->checklist->id)){
            $checklist_id   = $this->checklist->id;
        }else{
            $checklist_id   = '';
        }
        return [
                'name'          => $this->name,
                'checklist'     => new ChecklistResource($this->checklist),
                'items'         => new TemplateItemCollection(Item::where('checklist_id',$checklist_id)->get())
            ];
    }
}