<?php
namespace App\Http\Schema\History;
use Illuminate\Http\Resources\Json\Resource;

class GetListofHistoryResource extends Resource
{
    
    public function toArray($request)
    {
        return [
                'type'          => 'history',
                'id'            => $this->id,
                'attributes'    => [
                    'loggable_type'     => $this->loggable_type,
                    'loggable_id'       => $this->loggable_id,
                    'action'            => $this->action,
                    'kwuid'             => $this->kwuid,
                    'value'             => $this->value,
                    'created_at'        => $this->created_at,
                    'updated_at'        => $this->updated_at,
                ],
                'links' => [
                    'self' => route('detailhistory',['historyId' => $this->id])
                ]
        ];
    }
}