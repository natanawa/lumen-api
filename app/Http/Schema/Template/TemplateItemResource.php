<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\Resource;

class TemplateItemResource extends Resource
{
    
    public function toArray($request)
    {
        return [
            'description'   => $this->description,
            'urgenty'       => $this->urgenty,
            'due_interval'  => $this->due_interval,
            'due_unit'      => $this->due_unit
        ];
    }
}