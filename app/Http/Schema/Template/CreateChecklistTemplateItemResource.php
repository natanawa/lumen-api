<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;

class CreateChecklistTemplateItemResource extends Resource
{
   
    public function toArray($request)
    {
        return [
            'description'   => $this->description,
            'urgency'       => $this->urgency,
            'due_interval'  => $this->due_interval,
            'due_unit'      => $this->due_unit
        ];
    }
}