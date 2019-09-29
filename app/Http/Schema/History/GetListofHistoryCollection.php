<?php
namespace App\Http\Schema\History;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\History\GetListofHistoryResource;

class GetListofHistoryCollection extends ResourceCollection
{
    
    public function toArray($request)
    {
        return [
            'data'  => $this->collection->transform(function ($item){
                return new GetListofHistoryResource($item);
            })
        ];
    }

    public function withResponse($request, $response)
    {
        $res = json_decode($response->getContent(), true);
        $result['meta']['total']  = $res['meta']['total'];
        $result['meta']['count']  = $res['meta']['per_page'];
        $result['data']           = $res['data'];
        $result['links']          = $res['links'];
        $response->setContent(json_encode($result));
    }    
}