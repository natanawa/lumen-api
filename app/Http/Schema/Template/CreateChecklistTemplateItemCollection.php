<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Template\GetChecklistTemplateResource;

class CreateChecklistTemplateItemCollection extends ResourceCollection
{
    
    public function toArray($request)
    {
        return $this->collection->transform(function ($item){
                return new CreateChecklistTemplateItemResource($item);
            });
    }
}