<?php
namespace App\Http\Schema\Template;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Schema\Template\GetAssignTemplateResource;
use App\Http\Schema\Item\ItemAllResource;

class GetAssignTemplateCollection extends ResourceCollection
{

	public function toArray($request)
	{
		return [
			'data'  => $this->collection->transform(function ($item){
				return new GetAssignTemplateResource($item);
			}),
			'included'  => $this->collection->transform(function ($item){
				return new ItemAllResource($item);
			})
		];
	}

	public function withResponse($request, $response)
	{
		$res = json_decode($response->getContent(), true);
		$result['meta']['total']  = count($res['data']);
        $result['meta']['count']  = count($res['included']);
		$result['data']           = $res['data'];
		$result['included']       = $res['included'];
		$response->setContent(json_encode($result));
	}
}