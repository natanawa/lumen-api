<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Template\GetChecklistTemplateResource;

class GetChecklistTemplateCollection extends ResourceCollection
{
   
    public function toArray($request)
    {
        return [
            'data'  => $this->collection->transform(function ($item){
                return new GetChecklistTemplateResource($item);
            })
        ];
    }
}