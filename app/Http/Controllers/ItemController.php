<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Schema\Item\ItemCompleteCollection;
use App\Http\Schema\Item\CreateChecklistItemResource;
use App\Http\Schema\Item\ItemAllCollection;
use App\Http\Schema\Checklist\GetChecklistItemResource;
use App\Helper\Helper;
use App\User;
use App\Item;
use App\Checklist;
use App\Template;
use Carbon\Carbon;

class ItemController extends Controller{
 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function saved(Request $request, $id)
    {
        try{
            $param                  = json_decode($request->getContent(),true);
            $data                   = $param['data']['attribute'];
            $auth                   = $request->header();
            $token                  = $auth['authorization'];
            $user                   = User::where('token',str_replace('Bearer ','',$token[0]))->first();
            $data['user_id']        = $user->id;
            $data['checklist_id']   = $id;
            $result                 = Item::create($data);
            if($result){
                $datachecklist      = Checklist::find($id);
                return new CreateChecklistItemResource($datachecklist);
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function getchecklistitem($checklistId, $itemId)
    {
        try{
            $datachecklist      = Item::where('checklist_id',$checklistId)->where('id',$itemId)->first();
            if(count($datachecklist) > 0){
                return new GetChecklistItemResource($datachecklist);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function completeitems(Request $request)
    {
        try{
            $param              = json_decode($request->getContent(),true);
            $item_data          = array_values($param['data']);
            $update_data        = Item::whereIn('id', $item_data)->update(['is_completed' => true]);
            $result             = Item::whereIn('id',$item_data)->get();
            if(count($result) > 0){
                return new ItemCompleteCollection($result);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function incompleteitems(Request $request)
    {
        try{
            $param              = json_decode($request->getContent(),true);
            $item_data          = array_values($param['data']);
            $update_data        = Item::whereIn('id', $item_data)->update(['is_completed' => false]);
            $result             = Item::whereIn('id',$item_data)->get();
            if(count($result) > 0){
                return new ItemCompleteCollection($result);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function update(Request $request, $checklistId,$itemId)
    {
        try{
            $param                  = json_decode($request->getContent(),true);
            $data                   = $param['data']['attribute'];
            $item                   = Item::where('checklist_id',$checklistId)->where('id',$itemId)->first();
            $result                 = $item->update($data);
            if($result){
                $datachecklist      = Checklist::find($checklistId);
                return new CreateChecklistItemResource($datachecklist);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function bulkupdate(Request $request, $checklistId)
    {
        try{
            $param                  = json_decode($request->getContent(),true);
            $data                   = $param['data'];
            foreach($data as $items){
                $resultData   = array();
                $proses    = Item::where('checklist_id',$checklistId)->where('id',$items['id']);
                if($proses){   
                    $result    = $proses->update(
                        [
                            'description'   => $items['attributes']['description'],
                            'due'           => $items['attributes']['due'],
                            'urgency'       => $items['attributes']['urgency']
                        ]
                    );
                    if($result){
                        $resultData['status']  = 200;
                    }else{
                        $resultData['status']  = 403;
                    }    
                }else{
                    $resultData['status']  = 404;
                }
                $resultData['id']      = $items['id'];
                $resultData['action']  = 'update';
                $check[] = $resultData;

            }
            if(sizeof($check)>0){
                return Helper::response(200,'not',true,'',$check);
            }else{
                return Helper::response(404,'not',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function drop($checklistId,$itemId)
    {
        try{
            $item                   = Item::where('checklist_id',$checklistId)->where('id',$itemId)->first();
            if($item){
                $result             = $item->delete();
                return Helper::response(201,'error',false,'success');
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function summaries(Request $request)
    {
        try{
            $date       = Carbon::now();
            $today      = count(Item::whereDate('due',Carbon::now()->format('Y-m-d H:i:s'))->get());
            $past_due   = count(Item::whereDate('due','<',Carbon::now()->format('Y-m-d H:i:s'))->get());
            $this_week  = count(Item::whereBetween('due',[Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'),Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')])->get());
            $past_week  = count(Item::whereBetween('due',[Carbon::now()->subWeek()->subDay(7)->format('Y-m-d H:i:s'),Carbon::now()->subWeek()->format('Y-m-d H:i:s')])->get());
            $this_month = count(Item::whereBetween('due',[Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'),Carbon::now()->endOfMonth()->format('Y-m-d H:i:s')])->get());
            $past_month = count(Item::whereBetween('due',[Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'),Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->get());
            $total      = count(Item::all());
            $result     = [
                'today'         => $today,
                'past_due'      => $past_due,
                'this_week'     => $this_week,
                'past_week'     => $past_week,
                'this_month'    => $this_month,
                'past_month'    => $past_month,
                'total'         => $total
            ];
            return Helper::response(200,'not',true,'',$result);
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function allitems(Request $request)
    {
        try{
            $dataAllItem      = Item::paginate();
            if($dataAllItem){
                return new ItemAllCollection($dataAllItem);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function detailitems($itemId)
    {
        try{
            $dataAllItem      = Item::where('id',$itemId)->paginate();
            if($dataAllItem){
                return new ItemAllCollection($dataAllItem);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }
}
