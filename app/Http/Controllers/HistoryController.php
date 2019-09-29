<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Schema\History\GetListofHistoryCollection;
use App\Http\Schema\History\DetailHistoryCollection;
use App\Helper\Helper;
use App\History;

class HistoryController extends Controller{
    protected $historys;
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->historys   = new History;
    }

    public function getlisthistory(Request $request)
    {
        try{
            $datahistory      = History::paginate();
            if(count($datahistory) > 0){
                return new GetListofHistoryCollection($datahistory);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }
    
    public function detailhistory($historyId)
    {
        try{
            $datahistory      = History::where('id',$historyId)->paginate();
            if(count($datahistory) > 0){
                return new DetailHistoryCollection($datahistory);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }
}
