<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Template\DetailTemplateResource;

class DetailTemplateCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data'  => $this->collection->transform(function ($item){
                return new DetailTemplateResource($item);
            })
        ];
    }
    
    public function withResponse($request, $response)
    {
        $res = json_decode($response->getContent(), true);
        $result['data']           = $res['data'];
        $response->setContent(json_encode($result));
    }
}