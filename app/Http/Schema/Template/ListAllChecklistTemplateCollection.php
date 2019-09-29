<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Template\ListAllChecklistTemplateResource;

class ListAllChecklistTemplateCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data'  => $this->collection->transform(function ($item){
                return new ListAllChecklistTemplateResource($item);
            })
        ];
    }
    
    public function withResponse($request, $response)
    {
        $res = json_decode($response->getContent(), true);
        $result['meta']['total']  = $res['meta']['total'];
        $result['meta']['count']  = $res['meta']['per_page'];
        $result['links']          = $res['links'];
        $result['data']           = $res['data'];
        $response->setContent(json_encode($result));
    }
}